import './bootstrap';
import { makeProtectedRequest } from './protected';

import 'ol/ol.css'; // Import OpenLayers CSS
import * as ol from 'ol';
import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import OSM from 'ol/source/OSM';
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import Style from 'ol/style/Style';
import Icon from 'ol/style/Icon';
import { fromLonLat, toLonLat } from 'ol/proj';
import { defaults as defaultControls } from 'ol/control';

// Export OpenLayers classes for use in the Blade template
window.ol = {
    Map,
    View,
    TileLayer,
    VectorLayer,
    VectorSource,
    OSM,
    Feature,
    Point,
    Style,
    Icon,
    fromLonLat,
    toLonLat,
    defaultControls
};

window.makeProtectedRequest = makeProtectedRequest;

// Intercepter toutes les requêtes AJAX pour ajouter l'en-tête Authorization
(function(open) {
    XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
        open.call(this, method, url, async, user, pass);
        const token = localStorage.getItem('jwt_token');
        console.log('Intercepteur AJAX:', { method, url, token: token ? token.substring(0, 20) + '...' : null });
        if (token) {
            this.setRequestHeader('Authorization', `Bearer ${token}`);
        }
    };
})(XMLHttpRequest.prototype.open);
