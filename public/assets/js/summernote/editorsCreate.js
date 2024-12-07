$(document).ready(function() {
    $('#summernote').summernote({
        placeholder: 'Tapez votre contenu ici...',
        tabsize: 2,
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onInit: function() {
                // Appliquer des styles par défaut (en utilisant un CSS personnalisé)
                $('.note-editable').css({
                    'font-size': '11px', // Taille par défaut du texte
                    'line-height': '1.6',
                });
            }
        }
    });
});
