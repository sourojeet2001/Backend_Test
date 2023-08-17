/**
 * Js function for read more, read less toggle.
 */
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.bodytrim = {
    attach: function (context, settings) {
      if (context !== document) {
        return;
      }
      $('readToggle','.read-more-toggle', context).once('bodytrim').each(function () {
        var $toggle = $(this);
        var $content = $toggle.closest('.node__content');
        var $readLess = $content.find('.read-less-toggle');

        $toggle.on('click', function () {
          $content.toggleClass('expanded');
          $readLess.toggle();
        });

        $readLess.on('click', function () {
          $content.removeClass('expanded');
          $readLess.hide();
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
