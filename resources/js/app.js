import './bootstrap';
import Alpine from 'alpinejs';

import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Link,
    BlockQuote,
    List,
    Table,
    TableToolbar,
    Undo
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

window.Alpine = Alpine;
Alpine.start();

window.BlogCkeditor = {
    ClassicEditor,
    config: {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Link,
            BlockQuote,
            List,
            Table,
            TableToolbar,
            Undo
        ],
        toolbar: [
            'undo', 'redo',
            '|',
            'heading',
            '|',
            'bold', 'italic', 'underline', 'strikethrough',
            '|',
            'link', 'blockQuote',
            '|',
            'bulletedList', 'numberedList',
            '|',
            'insertTable'
        ],
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        }
    }
};