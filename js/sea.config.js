seajs.config({
    base:'/js',
    paths:{
        "themejs":"/themes/deebeis/js",
        "themepath":"/themes/deebeis"
    },
    alias:{
        "jquery": "jquery.min.js"
    },
    map: [
    [ /^(.*\.(?:css|js))(.*)$/i, '$1?v=20150403' ]
  ]
});
