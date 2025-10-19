# src/loaders.py
import pandas as pd
import json
from pathlib import Path
from typing import Dict
import re

BASE_DIR = Path(__file__).parent.parent / "data"

class LexiconLoader:
    def __init__(self):
        self.lexicons = {}
        self.emojis = {}
        self._load_all()

    def _load_all(self):
        """Charge tous les lexiques"""
        # Lexicons multilingues
        for lang in ['tunisian', 'french', 'arabic']:
            self.lexicons[lang] = self._load_emolex(f"{lang}_emolex.txt")

        # English fallback
        try:
            self.lexicons['english'] = self._load_onefile_emotions()
        except:
            self.lexicons['english'] = {}

        # Emojis
        self.emojis = self._load_emojis()
        print(f"📚 Loaded lexicons: {list(self.lexicons.keys())}")

    def _load_emolex(self, filename: str) -> Dict[str, Dict[str, int]]:
        """Charge NRC-EmoLex avec corrections tunisiennes"""
        try:
            file_path = BASE_DIR / "lexicons" / filename
            if not file_path.exists():
                print(f"⚠️ Warning: {file_path} not found")
                return {}

            # Détection colonne mots
            if 'tunisian' in filename:
                word_col = 'Tunisian Word'
            elif 'french' in filename:
                word_col = 'French Word'
            else:
                word_col = 'Arabic Word'

            df = pd.read_csv(file_path, sep='\t')
            if word_col not in df.columns:
                print(f"⚠️ Warning: {word_col} column not found in {filename}")
                return {}

            # Émotions standard NRC
            emotion_cols = ['anger', 'anticipation', 'disgust', 'fear', 'joy',
                          'negative', 'positive', 'sadness', 'surprise', 'trust']
            available_emotions = [col for col in emotion_cols if col in df.columns]

            lexicon = {}
            for _, row in df.iterrows():
                word = str(row[word_col]).lower().strip()
                if pd.isna(word) or not word:
                    continue

                emotions = {col: int(row.get(col, 0)) for col in available_emotions}
                lexicon[word] = emotions

            # 🔧 CORRECTIONS SPÉCIFIQUES TUNISIENNES
            if 'tunisian' in filename:
                self._apply_tunisian_corrections(lexicon)

            print(f"✅ Loaded {len(lexicon)} words from {filename}")
            return lexicon

        except Exception as e:
            print(f"❌ Error loading {filename}: {e}")
            return {}

    def _apply_tunisian_corrections(self, lexicon: Dict):
        """Corrections sémantiques pour dialecte tunisien"""
        corrections = {
            'mridha': {'disgust': 0, 'fear': 0},  # Déçu ≠ dégoût/peur
            'kif kif': {'trust': 1, 'positive': 1},  # Équivalent
            # Ajoute d'autres corrections selon tes besoins
        }

        for word, corr_scores in corrections.items():
            if word in lexicon:
                for emotion, value in corr_scores.items():
                    lexicon[word][emotion] = value
                print(f"🔧 Tunisian correction applied: {word}")

    def _load_onefile_emotions(self) -> Dict[str, Dict[str, int]]:
        """Fallback anglais"""
        lexicon = {}
        emotion_dir = BASE_DIR / "one_file_per_emotion"

        if not emotion_dir.exists():
            return {}

        emotion_mapping = {
            'anger': 'anger', 'joy': 'joy', 'sadness': 'sadness', 'fear': 'fear',
            'disgust': 'disgust', 'surprise': 'surprise', 'trust': 'trust'
        }

        for emotion_file in emotion_dir.glob("*.txt"):
            emotion_name = emotion_file.stem.lower()
            target_emotion = emotion_mapping.get(emotion_name)
            if not target_emotion:
                continue

            try:
                with open(emotion_file, 'r', encoding='utf-8', errors='ignore') as f:
                    for line in f:
                        parts = line.strip().split('\t')
                        if len(parts) >= 2:
                            word = parts[0].lower().strip()
                            score = int(parts[1])
                            if word not in lexicon:
                                lexicon[word] = {k: 0 for k in emotion_mapping.values()}
                            lexicon[word][target_emotion] = score
            except Exception as e:
                print(f"⚠️ Error loading {emotion_file}: {e}")

        return lexicon

    def _load_emojis(self) -> Dict[str, Dict[str, int]]:
        """Charge emojis avec scores émotionnels"""
        try:
            file_path = BASE_DIR / "emojis.json"
            if not file_path.exists():
                return {}

            with open(file_path, 'r', encoding='utf-8') as f:
                data = json.load(f)
            emoji_lexicon = {item['emoji']: item['emotions'] for item in data}
            print(f"😀 Loaded {len(emoji_lexicon)} emojis")
            return emoji_lexicon
        except Exception as e:
            print(f"⚠️ Error loading emojis: {e}")
            return {}

    def get_lexicon_for_language(self, lang: str) -> Dict[str, Dict[str, int]]:
        """Sélectionne lexique selon langue"""
        mapping = {
            'tunisian': 'tunisian',
            'fr': 'french',
            'ar': 'arabic',
            'en': 'english'
        }
        lexicon_key = mapping.get(lang, 'english')
        lexicon = self.lexicons.get(lexicon_key, {})
        print(f"📖 Using {lexicon_key} lexicon ({len(lexicon)} words) for lang '{lang}'")
        return lexicon
