var carte;
function init() {

    carte = new OpenLayers.Map({div: 'map'});


    var baseLayer = new OpenLayers.Layer.OSM();


    carte.addLayers([baseLayer]);
    carte.zoomToMaxExtent();
}