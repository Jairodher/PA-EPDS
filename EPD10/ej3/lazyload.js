(function ($) {

    $.fn.lazyload = function (options) {

        var settings = {
            threshold: 0,
            effect: "show",
            container: window
        };

        if (options) {
            $.extend(settings, options);
        }

        var elements = this;

        function update() {
            var counter = 0;

            elements.each(function () {
                var $this = $(this);

                if ($this.attr("data-original") &&
                    isInViewport($this, settings.threshold)) {

                    $this.hide();
                    $this.attr("src", $this.attr("data-original"));
                    $this.removeAttr("data-original");
                    $this[settings.effect]();
                } else {
                    counter++;
                }
            });

            if (counter === 0) {
                $(settings.container).off("scroll", update);
            }
        }

        function isInViewport(element, threshold) {
            var elementTop = element.offset().top;
            var elementBottom = elementTop + element.height();

            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            return elementBottom >= viewportTop - threshold &&
                   elementTop <= viewportBottom + threshold;
        }

        $(settings.container).on("scroll", update);
        update();

        return this;
    };

})(jQuery);
