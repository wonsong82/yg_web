jQuery(document).ready(function($){

  var mediaUploader;

  $('.btn-image-upload').click(function(e) {
    e.preventDefault();
    // If the uploader object has already been created, reopen the dialog
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
        text: 'Choose Image'
      }, multiple: false });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on('select', function() {
      attachment = mediaUploader.state().get('selection').first().toJSON();

      if(attachment.type!="image"){
        alert('Please select image file for Banner Image');
        mediaUploader.open();
        return;
      }


      $('#thumb-image').attr("src", attachment.url);
      $('#image-url').val(attachment.url);


    });
    // Open the uploader dialog
    mediaUploader.open();
  });


  $('.btn-submit.banner').click(function(e){
    e.preventDefault();
    $('#form').submit();
  });
});










/**
 * Created by Zeter on 11/10/2016.
 */
