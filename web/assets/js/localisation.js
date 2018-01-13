var carte;
function init() {

    carte = new OpenLayers.Map({div: 'myMap'});


    baseLayer = new OpenLayers.Layer.OSM();
    carte.addLayer(baseLayer);
    carte.zoomToMaxExtent();
}