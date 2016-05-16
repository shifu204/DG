define(function(require,exports,module){
    exports.check_mobile = function(mobile){
        var patrn = /^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$/;
        if (!patrn.exec(mobile)) {
            return false;
        }else {
            return true;
        }
    };
    
    exports.check_email = function(email){
        var patrn = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        if(!patrn.exec(email)){
            return false;
        }else {
            return true;
        }
    };
});

