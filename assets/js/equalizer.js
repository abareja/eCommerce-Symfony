(function($) {
    "use strict";
    $.fn.equalizer = function(options) {
        var $this = this;
        var resized = false;
        var settings = {
            use_tallest: false
        }

        function equilizeItems(items, height) {
            for (var i = 0; i < items.length; i++) {
                items[i].css('height', height);        
            }
        }

        function computeHeight(item, height) {
            if (settings.use_tallest) {
                return !height || height < item.outerHeight(false) ? item.outerHeight(false) : height;
            } else {
                return !height || height > item.outerHeight(false) ? item.outerHeight(false) : height;
            }           
        }

        function imgLoaded(items, callback) {
            if (items.data('img-loaded')) {
                return callback();
            }

            function loaded() {
                toLoad -= 1;

                if (toLoad === 0) {
                    callback();
                    items.data('img-loaded', true);
                }

            }

            var images = items.find('img');
            var toLoad = images.length;
            
            if (toLoad === 0) {
                return callback();
            }

            for (var i = 0; i < images.length; i++) {
                var image = images[i];
                var $image = $(image);

                if (!$image.attr('src')) {
                    loaded();
                }
                else if (image.complete || image.readyState === 4) {
                    loaded();
                }
                else {
                    $image.one("load", loaded);
                }
            }
        }

        function equalize() {
            var $this = $(this);
            var items = $this.find('[data-equalizer-watch]')
            if (items.length === 0) return;
            
            imgLoaded($this, function() {
                items.css({ height: 'auto' });

                var watches = {};

                for (var i  = 0; i < items.length; i++) {
                    var el = $(items[i]);
                    var watch = el.data('equalizer-watch');
                    if (watches[watch] == undefined) {
                        watches[watch] = [el];
                    } else {
                        watches[watch].push(el);
                    }
                }

                $.each(watches, function() {
                    var height = computeHeight(this[0], 0);

                    for (var i = 0; i < this.length; i++)
                    {

                        height = computeHeight(this[i], height);            
                    }

                    equilizeItems(this, height);               
                });
            });
        }

        function update(forced) {
            if (resized || forced) {
                $this.each(function() {
                    equalize.call(this);
                    resized = false;
                });
            }
        }        

        if (options) {
            $.extend(settings, options); 
        }

        $(window).resize(function() { 
            resized = true;
        });

        setInterval(update, 100);

        update(true);        
    }
})(jQuery);