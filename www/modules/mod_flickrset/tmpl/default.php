<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
<!--

function img(photo, size) {
 return '<a href="http://www.planetangel.net/photos"><img src="http://farm' +
        photo.farm +
        '.static.flickr.com/' +
        photo.server +
        '/' +
        photo.id +
        "_" +
        photo.secret +
        '_' +
        size +
        '.jpg' +
        '" alt="' +
        photo.title +
        '" />';
}

function jsonFlickrApi(rsp) {

 if (rsp.stat != "ok"){
  return;
 }

 // First, a random photo
 randomPhoto = rsp.photoset.photo[Math.floor(Math.random() * rsp.photoset.photo.length)];

 var s = '<table border="0" class="photostream"><tr><td class="photostream-main">';
 s += img(randomPhoto, 'm');
 s += '</td><td class="photostream-small>';

 var max = rsp.photoset.photo.length;
 if (max > 3) {
  max = 3;
 }

 for (var i=0; i < max; i++) {
  photo = rsp.photoset.photo[i];
  s += img(photo, 's');
 }

 s += '</td></tr></table>';
 document.writeln(s);

}
// -->
</script>
<script type="text/javascript" src="http://api.flickr.com/services/rest/?format=json&method=flickr.photosets.getPhotos&user_id=<?php echo $userid; ?>&api_key=<?php echo $apikey; ?>&photoset_id=<?php echo $setid; ?>"></script>