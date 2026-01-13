<script src="{{ asset('all.js') }}"></script>
<!-- Stack array for including inline js or scripts -->
@stack('script')

<script src="{{ asset('dist/js/theme.js') }}"></script>

<script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
<script src="{{ asset('plugins/summernote/dist/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('js/layouts.js') }}"></script> 

<script src="{{ asset('plugins/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-minicolors/jquery.minicolors.min.js') }}"></script>
<script src="{{ asset('plugins/datedropper/datedropper.min.js') }}"></script>
<script src="{{ asset('js/form-picker.js') }}"></script>
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
 <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
 <!-- Charger CKEditor via CDN -->

<!-- Intégration des fichiers JavaScript de TinyMCE -->
<?php
    /*
        <script>
            // Initialisation de TinyMCE
            tinymce.init({
                selector: '#myTextarea',
                plugins: 'advlist autolink lists link image charmap print preview anchor',
                toolbar: 'undo redo | formatselect | ' +
                         'bold italic backcolor | alignleft aligncenter ' +
                         'alignright alignjustify | bullist numlist outdent indent | ' +
                         'removeformat | help',
                content_css: 'plugins/tinymce/js/tinymce/skins/content/default/content.min.css'
            });
        </script>
        <script>
           tinymce.init({
             selector: 'textarea#myeditorinstance', // Replace this CSS selector to match the placeholder element for TinyMCE
             plugins: 'powerpaste advcode table lists checklist',
             toolbar: 'undo redo | blocks| bold italic | bullist numlist checklist | code | table',
             content_css: 'plugins/tinymce/js/tinymce/skins/content/default/content.min.css'
           });
        </script>
    */ 
?>

<script>
    var menu_bar = 'file edit view insert format tools table help';
        tinymce.init({
            selector: 'textarea#myeditorinstance', // Replace this CSS selector to match the placeholder element for TinyMCE
            
            valid_elements: '*[*]',
            relative_urls: false,
            remove_script_host: false,
            language: 'fr_FR',
            menubar: menu_bar,
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code codesample fullscreen",
                "insertdatetime media table paste imagetools"
            ],
            toolbar: 'fullscreen code preview | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | image media link | outdent indent',
            content_css: ['plugins/tinymce/js/tinymce/skins/content/default/editor_content.css'],
        });
</script>