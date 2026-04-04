import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const TOOLBAR_PRESETS = {
    full: [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['blockquote', 'code-block'],
        ['link', 'image', 'video'],
        ['clean'],
    ],
    compact: [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'image'],
        ['clean'],
    ],
};

function initEditor(el) {
    if (el.__quill) {
        return;
    }

    const presetName = el.dataset.quillToolbar || 'full';
    const toolbar = TOOLBAR_PRESETS[presetName] ?? TOOLBAR_PRESETS.full;

    const quill = new Quill(el, {
        theme: 'snow',
        modules: { toolbar },
    });

    const targetId = el.dataset.quillTarget;
    const target = targetId ? document.getElementById(targetId) : null;
    if (target) {
        if (target.value) {
            quill.clipboard.dangerouslyPasteHTML(target.value);
        }
        quill.on('text-change', () => {
            target.value = quill.getLength() > 1 ? quill.root.innerHTML : '';
        });
    }

    el.__quill = quill;
}

document.querySelectorAll('[data-quill]').forEach(initEditor);
