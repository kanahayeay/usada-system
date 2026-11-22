document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('glosariumModal');
    const form = document.getElementById('glosariumForm');

    if (!modal || !form) return;

    const modalTitle = document.getElementById('glModalTitle');
    const modalDescription = document.getElementById('glModalDescription');
    const submitButton = document.getElementById('glSubmitButton');

    const fields = {
        istilah: document.getElementById('glIstilah'),
        varian: document.getElementById('glVarian'),
        bahasa: document.getElementById('glBahasa'),
        kategori: document.getElementById('glKategori'),
        makna_indonesia: document.getElementById('glMakna'),
        contoh_konteks: document.getElementById('glContoh')
    };

    const saveUrl = form.getAttribute('action');

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('gl-modal-open');

        setTimeout(() => {
            fields.istilah.focus();
        }, 150);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('gl-modal-open');
    }

    function resetForm() {
        form.reset();
        form.action = saveUrl;

        // Kembalikan label CSRF jika pernah berubah
        modalTitle.textContent = 'Tambah Istilah';
        modalDescription.textContent =
            'Masukkan istilah baru ke dalam Glosarium.';

        submitButton.innerHTML =
            '<i class="bi bi-check-lg"></i> Simpan Istilah';
    }

    // TOMBOL TAMBAH
    const btnTambah = document.getElementById('btnTambahGlosarium');

    if (btnTambah) {
        btnTambah.addEventListener('click', function () {
            resetForm();
            openModal();
        });
    }

    // TOMBOL EDIT
    document.querySelectorAll('.gl-edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            try {
                const item = JSON.parse(this.dataset.glosarium);

                resetForm();

                modalTitle.textContent = 'Edit Istilah Glosarium';
                modalDescription.textContent =
                    'Perbarui informasi istilah Glosarium.';

                form.action = saveUrl.replace(
                    /simpan$/,
                    'update/' + encodeURIComponent(item.id_glosarium)
                );

                Object.keys(fields).forEach(key => {
                    fields[key].value = item[key] ?? '';
                });

                submitButton.innerHTML =
                    '<i class="bi bi-save"></i> Simpan Perubahan';

                openModal();

            } catch (error) {
                console.error('Gagal membuka data Glosarium:', error);
                alert('Data Glosarium gagal dibuka.');
            }
        });
    });

    // TUTUP MODAL
    modal.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', closeModal);
    });

    // TUTUP DENGAN TOMBOL ESC
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    // KONFIRMASI HAPUS GLOSARIUM
    const deleteModal = document.getElementById('glDeleteModal');
    const deleteName = document.getElementById('glDeleteName');
    const confirmDelete = document.getElementById('glConfirmDelete');

    let selectedDeleteForm = null;

    if (deleteModal) {
        document.querySelectorAll('.gl-delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                selectedDeleteForm = this;
                deleteName.textContent = this.dataset.istilah || 'istilah ini';

                deleteModal.classList.add('is-open');
                deleteModal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('gl-modal-open');
            });
        });

        function closeDeleteModal() {
            deleteModal.classList.remove('is-open');
            deleteModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('gl-modal-open');
            selectedDeleteForm = null;
        }

        deleteModal.querySelectorAll('[data-close-delete]').forEach(button => {
            button.addEventListener('click', closeDeleteModal);
        });

        confirmDelete.addEventListener('click', function () {
            if (selectedDeleteForm) {
                selectedDeleteForm.submit();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' &&
                deleteModal.classList.contains('is-open')) {
                closeDeleteModal();
            }
        });
    }

    // TOAST NOTIFICATION
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
            toastClose.addEventListener('click', () => {
                clearTimeout(toastTimer);
                closeToast();
            });
        }
    }
});