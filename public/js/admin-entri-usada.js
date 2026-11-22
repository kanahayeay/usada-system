document.addEventListener('DOMContentLoaded', function () {

    // ELEMENT MODAL DAN FORM
    const modal = document.getElementById('entriUsadaModal');
    const form = document.getElementById('entriUsadaForm');

    const deleteModal = document.getElementById('hapusEntriModal');
    const deleteForm = document.getElementById('hapusEntriForm');

    if (!modal || !form) return;

    const modalTitle = document.getElementById('entriUsadaModalTitle');
    const saveUrl = form.dataset.saveUrl;

    // FIELD FORM ENTRI USADA
    const fields = [
        'nama_penyakit',
        'id_wuku',
        'id_saptawara',
        'id_pancawara',
        'gejala',
        'penyebab',
        'ritual',
        'teks_asli',
        'lembar_awal',
        'lembar_akhir'
    ];

    // ========================================
    // FUNGSI BUKA DAN TUTUP MODAL
    // ========================================

    function openModal(target) {
        if (!target) return;

        target.classList.add('is-open');
        target.setAttribute('aria-hidden', 'false');
        document.body.classList.add('gl-modal-open');
    }

    function closeModal(target) {
        if (!target) return;

        target.classList.remove('is-open');
        target.setAttribute('aria-hidden', 'true');

        // Kunci scroll hanya jika tidak ada modal lain terbuka
        const modalMasihTerbuka =
            document.querySelector('.gl-modal.is-open');

        if (!modalMasihTerbuka) {
            document.body.classList.remove('gl-modal-open');
        }
    }

    // ========================================
    // RESET FORM
    // ========================================

    function resetForm() {
        form.reset();
        form.action = saveUrl;
        modalTitle.textContent = 'Tambah Entri Usada';
    }

    // ========================================
    // TAMBAH ENTRI USADA
    // ========================================

    window.bukaModalTambah = function () {
        resetForm();
        openModal(modal);
    };

    // ========================================
    // EDIT ENTRI USADA
    // ========================================

    window.bukaModalEdit = function (data) {
        resetForm();

        modalTitle.textContent = 'Edit Entri Usada';

        form.action = form.dataset.updateUrl + '/' + data.id_entri;

        fields.forEach(field => {
            const input = document.getElementById(field);

            if (input) {
                input.value = data[field] ?? '';
            }
        });

        openModal(modal);
    };

    // ========================================
    // TUTUP MODAL TAMBAH / EDIT
    // ========================================

    modal.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', function () {
            closeModal(modal);
        });
    });

    // ========================================
    // HAPUS ENTRI USADA
    // ========================================

    window.bukaModalHapus = function (id, nama) {
        if (!deleteModal || !deleteForm) return;

        deleteForm.action = deleteForm.dataset.deleteUrl + '/' + id;

        const namaHapus = document.getElementById('namaEntriHapus');

        if (namaHapus) {
            namaHapus.textContent = nama;
        }

        openModal(deleteModal);
    };

    // ========================================
    // TUTUP MODAL HAPUS
    // ========================================

    if (deleteModal) {
        deleteModal.querySelectorAll('[data-close-delete]').forEach(button => {
            button.addEventListener('click', function () {
                closeModal(deleteModal);
            });
        });
    }

    // ========================================
    // TUTUP MODAL DENGAN ESC
    // ========================================

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (modal.classList.contains('is-open')) {
                closeModal(modal);
            }

            if (deleteModal && deleteModal.classList.contains('is-open')) {
                closeModal(deleteModal);
            }
        }
    });

    // ========================================
    // TOAST NOTIFICATION
    // ========================================

    const toast = document.getElementById('glToast');
    const toastClose = document.getElementById('glToastClose');

    if (toast) {
        function closeToast() {
            toast.classList.add('gl-toast-out');

            setTimeout(() => {
                toast.remove();
            }, 300);
        }

        const toastTimer = setTimeout(closeToast, 4000);

        if (toastClose) {
            toastClose.addEventListener('click', function () {
                clearTimeout(toastTimer);
                closeToast();
            });
        }
    }

});