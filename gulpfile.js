var gulp = require('gulp');

gulp.task( 'generate_icons', function(end) {
    var icons = require('./assets/icomoon/selection.json');
    for(var i=0; i < icons.icons.length; i++ ) {
        var prop = icons.icons[i].properties;
        var names = prop.name.split(', ');
        for(var j=0; j < names.length; j++ ) {
            console.log(names[j] + ": \"\\" + parseInt(prop.code).toString(16) + "\",");
        }
    }

    end();
});