window.addEvent('domready', function(){ 
  new Accordion($$('h4.joomgallery-toggler'), $$('div.joomgallery-slider'), 
    {onActive: function(toggler, i) { 
      toggler.addClass('joomgallery-toggler-down'); 
      toggler.removeClass('joomgallery-toggler'); },
     onBackground: function(toggler, i) { 
      toggler.addClass('joomgallery-toggler'); 
      toggler.removeClass('joomgallery-toggler-down'); },
      duration: 300,display:-1,show:0,opacity: false,alwaysHide: true}); });
          
/*
Documentation copied from Joomla! 1.5 mootools_uncompressed.js
Line 6952 ff.

Class: Accordion
  The Accordion class creates a group of elements that are toggled when their handles are clicked. 
  When one elements toggles in, the others toggles back.
  Inherits methods, properties, options and events from <Fx.Elements>.

Note:
  The Accordion requires an XHTML doctype.

Arguments:
  togglers - required, a collection of elements, the elements handlers that will be clickable.
  elements - required, a collection of elements the transitions will be applied to.
  options - optional, see options below, and <Fx.Base> options and events.

Options:
  show - integer, the Index of the element to show at start.
  display - integer, the Index of the element to show at start (with a transition). defaults to 0.
  fixedHeight - integer, if you want the elements to have a fixed height. defaults to false.
  fixedWidth - integer, if you want the elements to have a fixed width. defaults to false.
  height - boolean, will add a height transition to the accordion if true. defaults to true.
  opacity - boolean, will add an opacity transition to the accordion if true. defaults to true.
  width - boolean, will add a width transition to the accordion if true. defaults to false, css mastery 
          is required to make this work!
  alwaysHide - boolean, will allow to hide all elements if true, instead of always keeping one element shown. 
               defaults to false.

Events:
  onActive - function to execute when an element starts to show
  onBackground - function to execute when an element starts to hide
*/
    