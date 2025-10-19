# src/analyzer.py
import re
from typing import Dict, Any, List, Tuple
from fuzzywuzzy import fuzz
from langdetect import detect
from .loaders import LexiconLoader
from collections import defaultdict
from langdetect.lang_detect_exception import LangDetectException

class SentimentAnalyzer:
    def __init__(self, threshold_fuzzy: int = 80):
        self.loader = LexiconLoader()
        self.threshold_fuzzy = threshold_fuzzy
        self.emotion_weights = {
            'positive': 1.5, 'joy': 1.3, 'trust': 1.1,
            'negative': 1.0, 'anger': 1.0, 'sadness': 1.0,
            'fear': 0.9, 'disgust': 0.7, 'anticipation': 0.5, 'surprise': 0.3
        }
        print(f"✅ Analyzer ready with {len(self.loader.lexicons)} lexicons")

    def detect_language(self, text: str) -> str:
        """Détection multi-pass pour dialecte tunisien"""
        try:
            text_lower = text.lower()

            # 🔍 HEURISTIQUE TUNISIENNE PRIORITAIRE
            tunisian_indicators = [
                'farhèn', 'farhen', 'yhebb', 'kifkif', 'kif kif', 'mridha', 'nebki',
                'khayef', 'chwaya', 'barcha', 'zwin', 'n7eb', 'yallah', 'khouya'
            ]

            if any(indicator in text_lower for indicator in tunisian_indicators):
                print(f"🇹🇳 Tunisian detected by keywords")
                return 'tunisian'

            # Nettoyage pour langdetect
            clean_text = re.sub(r'[^\w\s\u0600-\u06FF]', ' ', text)
            clean_text = ' '.join(clean_text.split()[:50])

            if len(clean_text) < 10:
                return 'unknown'

            lang = detect(clean_text)

            # Mapping dialectes
            if lang in ['ar', 'sw']:
                return 'tunisian' if any(indicator in text_lower for indicator in tunisian_indicators) else 'ar'

            return lang

        except LangDetectException:
            # Fallback heuristique
            text_lower = text.lower()
            if any(indicator in text_lower for indicator in tunisian_indicators[:5]):
                return 'tunisian'
            return 'unknown'
        except Exception as e:
            print(f"⚠️ Lang detection failed: {e}")
            return 'unknown'

    def preprocess_text(self, text: str) -> Tuple[List[str], List[str]]:
        """Tokenisation avancée"""
        # Nettoie URLs/mentions
        text = re.sub(r'http\S+|www\S+|@\w+', '', text)

        # Tokenisation (latin + arabe)
        words = re.findall(r'\b[\w\u0600-\u06FF\u0750-\u077F]{2,}\b', text.lower())

        # Emojis
        emojis = re.findall(r'[\U0001F600-\U0001F64F\U0001F300-\U0001F5FF\u2600-\u26FF\u2700-\u27BF]', text)

        return words, emojis

    def find_matches(self, words: List[str], lexicon: Dict) -> Dict:
        """Matching exact + fuzzy optimisé"""
        matched_words = {'exact': [], 'fuzzy': []}

        for word in words:
            if word in lexicon:
                matched_words['exact'].append(word)
                continue

            # Fuzzy matching (limité)
            for lex_word in list(lexicon.keys())[:100]:
                similarity = fuzz.ratio(word, lex_word)
                if similarity >= self.threshold_fuzzy:
                    matched_words['fuzzy'].append({
                        'word': word, 'matched': lex_word, 'similarity': similarity
                    })
                    break

        return matched_words

    def extract_emoji_scores(self, emojis: List[str]) -> Dict[str, float]:
        """Analyse emojis"""
        scores = defaultdict(float)
        for emoji in emojis:
            if emoji in self.loader.emojis:
                for emotion, score in self.loader.emojis[emoji].items():
                    scores[emotion] += score * 0.3  # Poids léger
        return dict(scores)

    def analyze(self, text: str) -> Dict[str, Any]:
        """Analyse complète avec pondération tunisienne"""
        if not text or len(text.strip()) < 3:
            return self._empty_result()

        lang = self.detect_language(text)
        words, emojis = self.preprocess_text(text)
        lexicon = self.loader.get_lexicon_for_language(lang)

        if not lexicon:
            return self._empty_result(lang=lang)

        # Matching
        matched = self.find_matches(words, lexicon)

        # Scores émotionnels
        emotion_scores = defaultdict(float)

        # Mots matchés
        for match_type, matches in matched.items():
            weight = 1.0 if match_type == 'exact' else 0.6
            words_to_process = matches if match_type == 'exact' else [m['matched'] for m in matches]

            for word in words_to_process:
                if word in lexicon:
                    for emotion, score in lexicon[word].items():
                        if score > 0:  # Seulement émotions présentes
                            emotion_scores[emotion] += score * weight

        # Emojis
        emoji_scores = self.extract_emoji_scores(emojis)
        for emotion, score in emoji_scores.items():
            emotion_scores[emotion] += score

        # NORMALISATION PONDÉRÉE
        total_raw = sum(emotion_scores.values()) or 1
        normalized_scores = {k: v / total_raw for k, v in emotion_scores.items()}

        # SCORES PONDÉRÉS (TUNISIEN OPTIMISÉ)
        weighted_scores = {k: normalized_scores.get(k, 0) * self.emotion_weights.get(k, 1.0)
                          for k in self.emotion_weights.keys()}

        # SCORE GLOBAL AMÉLIORÉ
        positive_emotions = ['positive', 'joy', 'trust']
        negative_emotions = ['negative', 'anger', 'sadness', 'fear', 'disgust']

        pos_weight = sum(weighted_scores.get(e, 0) for e in positive_emotions)
        neg_weight = sum(weighted_scores.get(e, 0) for e in negative_emotions)

        overall_score = (pos_weight - neg_weight) / (pos_weight + neg_weight + 1e-8)

        # Émotion dominante
        dominant_emotion = max(weighted_scores, key=weighted_scores.get, default='neutral')

        # Confiance
        match_count = len(matched['exact']) + len(matched['fuzzy'])
        confidence = min(match_count / max(len(words), 1), 1.0)

        return {
            'language': lang,
            'scores': {k: int(round(v * 100)) for k, v in normalized_scores.items()},
            'overall_sentiment_score': float(overall_score),
            'dominant_emotion': dominant_emotion,
            'confidence': float(confidence),
            'matched_words': matched,
            'raw_scores': dict(normalized_scores),
            'weighted_scores': dict(weighted_scores),
            'word_count': len(words),
            'emoji_count': len(emojis)
        }

    def _empty_result(self, lang='unknown'):
        """Résultat par défaut"""
        return {
            'language': lang,
            'scores': {k: 0 for k in self.emotion_weights.keys()},
            'overall_sentiment_score': 0.0,
            'dominant_emotion': 'neutral',
            'confidence': 0.0,
            'matched_words': {'exact': [], 'fuzzy': []},
            'raw_scores': {},
            'weighted_scores': {},
            'word_count': 0,
            'emoji_count': 0
        }
