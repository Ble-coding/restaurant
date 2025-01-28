$(document).ready(function () {
    function initializeSummernote(selector, lang = 'en-US') {
        $(selector).summernote({
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
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
            lang: lang, // Définit la langue de l'éditeur
            callbacks: {
                onInit: function () {
                    // Appliquer des styles par défaut (via CSS)
                    $(selector)
                        .next('.note-editor')
                        .find('.note-editable')
                        .css({
                            'font-size': '11px', // Taille par défaut
                            'line-height': '1.6',
                        });
                },
            },
        });
    }

    // Initialiser Summernote pour chaque champ
    initializeSummernote('#summernote', 'fr-FR'); // Contenu en français
    initializeSummernote('#summernote_en', 'en-US'); // Contenu en anglais
});
