//polyfill

if (!String.prototype.trim) {
  String.prototype.trim = function () {
    return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
  };
}

function resError(source)
{
    //this is to handle all script/css/resource error fallbacks
    var docRoot = typeof(documentRoot) != undefined ? documentRoot : "/Econs-Forex-Game/";
    var res = source.trim().split("/").slice(-1)[0];

    console.log("onError triggered while loading", res)

    var fallbacks =
    {
        //javascript
        "jquery.min.js": "js/jquery.min.js",
        "materialize.min.js" : "js/materialize.min.js",

        //stylesheets NOTE onError doesn't trigger well for stylesheets
        "materialize.min.css": "css/materialize.min.css",
        "icon?family=Material+Icons": "css/material-icon-font.css"
    }

    return docRoot + fallbacks[res];
}
