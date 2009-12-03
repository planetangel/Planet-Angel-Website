<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
<!--

function img(photo, width) {
 return '<a href="<?php echo $url; ?>"><img border="0" width="' + width + '" src="http://farm' +
        photo.farm +
        '.static.flickr.com/' +
        photo.server +
        '/' +
        photo.id +
        "_" +
        photo.secret +
        '.jpg' +
        '" alt="' +
        photo.title +
        '" /></a>';
}

function jsonFlickrApi(rsp) {

 if (rsp.stat != "ok"){
  return;
 }

 // First, a random photo
 randomPhoto = rsp.photoset.photo[Math.floor(Math.random() * rsp.photoset.photo.length)];
 document.writeln(img(randomPhoto, <?php echo $width; ?>));

}
// -->
</script>
<script type="text/javascript" src="http://api.flickr.com/services/rest/?format=json&method=flickr.photosets.getPhotos&user_id=<?php echo $userid; ?>&api_key=<?php echo $apikey; ?>&photoset_id=<?php echo $setid; ?>"></script>