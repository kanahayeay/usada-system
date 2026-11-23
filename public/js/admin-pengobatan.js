document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // =========================================
    // ELEMENT
    // =========================================
    const modal = document.getElementById('pengobatanModal');
    const form = document.getElementById('pengobatanForm');

    const deleteModal = document.getElementById('hapusPengobatanModal');
    const deleteForm = document.getElementById('hapusPengobatanForm');

    const modalTitle = document.getElementById('pengobatanModalTitle');
    const daftarPengobatan = document.getElementById('daftarPengobatan');
    const tombolTambah = document.getElementById('tambahKelompokPengobatan');
    const jumlahPengobatan = document.getElementById('jumlahPengobatan');
    const tombolSimpan = document.getElementById('btnSimpanPengobatan');

    const saveUrl = form ? (form.dataset.saveUrl || form.action) : '';
    let modeForm = 'tambah';
    let sedangMengirim = false;

    // =========================================
    // MODAL
    // =========================================
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

        if (!document.querySelector('.gl-modal.is-open')) {
            document.body.classList.remove('gl-modal-open');
        }
    }

    // =========================================
    // VALIDATION MESSAGE
    // =========================================
    function getValidationBox() {
        if (!form) return null;

        let box = form.querySelector('#pengobatanClientErrors');

        if (!box) {
            box = document.createElement('div');
            box.id = 'pengobatanClientErrors';
            box.className = 'alert alert-danger d-none';
            box.setAttribute('role', 'alert');

            const anchor = daftarPengobatan ||
                form.querySelector('.modal-body') ||
                form.firstChild;

            if (anchor && anchor.parentNode) {
                anchor.parentNode.insertBefore(box, anchor);
            } else {
                form.prepend(box);
            }
        }

        return box;
    }

    function clearValidationErrors() {
        const box = form
            ? form.querySelector('#pengobatanClientErrors')
            : null;

        if (box) {
            box.classList.add('d-none');
            box.replaceChildren();
        }

        if (form) {
            form.querySelectorAll('.is-invalid').forEach(function (field) {
                field.classList.remove('is-invalid');
                field.removeAttribute('aria-invalid');
            });
        }
    }

    function showValidationErrors(messages, firstInvalid) {
        const box = getValidationBox();
        if (!box) return;

        box.replaceChildren();

        const heading = document.createElement('strong');
        heading.textContent = 'Periksa kembali data berikut:';
        box.appendChild(heading);

        const list = document.createElement('ul');
        list.className = 'mb-0 mt-2';

        messages.forEach(function (message) {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });

        box.appendChild(list);
        box.classList.remove('d-none');

        if (firstInvalid) {
            const firstGroup = firstInvalid.closest('.pengobatan-group');

            if (firstGroup) {
                firstGroup.open = true;
            }

            firstInvalid.focus({ preventScroll: true });
            firstInvalid.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        } else {
            box.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    }

    // =========================================
    // GROUP COUNTER AND INPUT NAME
    // =========================================
    function updateJumlahPengobatan() {
        if (!daftarPengobatan || !jumlahPengobatan) return;

        const jumlah = daftarPengobatan.querySelectorAll(
            '.pengobatan-group'
        ).length;

        jumlahPengobatan.textContent = jumlah + ' pengobatan';
    }

    function perbaruiKelompok() {
        if (!daftarPengobatan) return;

        const groups = daftarPengobatan.querySelectorAll(
            '.pengobatan-group'
        );

        groups.forEach(function (group, index) {
            const title = group.querySelector(
                '.pengobatan-group-title'
            );

            if (title) {
                title.textContent = 'Pengobatan ' + (index + 1);
            }

            group.querySelectorAll('[data-field]').forEach(function (input) {
                input.name =
                    'pengobatan[' + index + '][' + input.dataset.field + ']';
            });

            const tombolHapus = group.querySelector(
                '.btn-hapus-kelompok'
            );

            if (tombolHapus) {
                tombolHapus.style.display =
                    (modeForm === 'edit' || groups.length <= 1)
                        ? 'none'
                        : '';
            }
        });

        updateJumlahPengobatan();
    }

    // =========================================
    // CREATE GROUP
    // =========================================
    function buatKelompokPengobatan(data = {}) {
        if (!daftarPengobatan) return;

        const group = document.createElement('details');
        group.className = 'pengobatan-group';
        group.open = true;

        // HEADER ACCORDION
        const summary = document.createElement('summary');
        summary.className = 'pengobatan-group-header';

        const judul = document.createElement('span');
        judul.className = 'pengobatan-group-title';

        const icon = document.createElement('i');
        icon.className = 'bi bi-chevron-down';
        icon.setAttribute('aria-hidden', 'true');

        summary.append(judul, icon);

        // BODY ACCORDION
        const body = document.createElement('div');
        body.className = 'pengobatan-group-body';

        // DAFTAR FIELD
        const fields = [
            {
                name: 'jenis',
                label: 'Jenis Pengobatan',
                placeholder: 'Contoh: Jamu, Boreh, Loloh, Kompres...',
                required: true,
                rows: 2
            },
            {
                name: 'bahan_asli',
                label: 'Bahan Asli',
                placeholder: 'Masukkan bahan-bahan pengobatan sesuai naskah Usada Bali...',
                required: true,
                rows: 3
            },
            {
                name: 'bagian_tubuh',
                label: 'Bagian Tubuh',
                placeholder: 'Masukkan bagian tubuh yang berkaitan dengan pengobatan...',
                required: true,
                rows: 2
            },
            {
                name: 'keterangan',
                label: 'Keterangan / Kondisi (Opsional)',
                placeholder: 'Masukkan keterangan atau kondisi khusus jika tercantum dalam naskah...',
                required: false,
                rows: 2
            },
            {
                name: 'cara',
                label: 'Cara Pengobatan',
                placeholder: 'Jelaskan cara pengolahan atau penerapan pengobatan sesuai naskah Usada Bali...',
                required: true,
                rows: 3
            }
        ];

        // MEMBUAT INPUT
        fields.forEach(function (field) {
            const wrapper = document.createElement('div');
            wrapper.className = 'mb-3';

            const label = document.createElement('label');
            label.className = 'form-label fw-semibold';
            label.textContent = field.label;

            const input = document.createElement('textarea');

            const inputId = 'pengobatan-' + field.name + '-' +
                (window.crypto && crypto.randomUUID
                    ? crypto.randomUUID()
                    : Date.now() + '-' + Math.random().toString(16).slice(2));

            input.id = inputId;
            label.htmlFor = inputId;
            input.className = 'form-control';
            input.dataset.field = field.name;
            input.placeholder = field.placeholder;
            input.rows = field.rows;
            input.required = field.required;
            input.value = data[field.name] == null
                ? ''
                : String(data[field.name]);

            input.addEventListener('input', function () {
                input.classList.remove('is-invalid');
                input.removeAttribute('aria-invalid');
            });

            wrapper.append(label, input);
            body.appendChild(wrapper);
        });

        // FOOTER KELOMPOK
        const footer = document.createElement('div');
        footer.className = 'd-flex justify-content-end';

        const tombolHapus = document.createElement('button');
        tombolHapus.type = 'button';
        tombolHapus.className =
            'btn btn-sm btn-outline-danger btn-hapus-kelompok';

        tombolHapus.innerHTML =
            '<i class="bi bi-trash" aria-hidden="true"></i> Hapus Kelompok';

        tombolHapus.addEventListener('click', function () {
            if (modeForm === 'edit') return;

            const jumlah = daftarPengobatan.querySelectorAll(
                '.pengobatan-group'
            ).length;

            if (jumlah <= 1) return;

            group.remove();

            clearValidationErrors();
            perbaruiKelompok();
        });

        footer.appendChild(tombolHapus);
        body.appendChild(footer);

        group.append(summary, body);
        daftarPengobatan.appendChild(group);

        perbaruiKelompok();
    }

    // =========================================
    // RESET FORM
    // =========================================
    function resetForm() {
        if (!form) return;

        form.reset();
        form.action = saveUrl;
        sedangMengirim = false;

        if (daftarPengobatan) {
            daftarPengobatan.replaceChildren();
        }

        modeForm = 'tambah';

        if (tombolTambah) {
            tombolTambah.style.display = '';
        }

        if (jumlahPengobatan) {
            jumlahPengobatan.textContent = '0 pengobatan';
        }

        if (modalTitle) {
            modalTitle.textContent = 'Tambah Pengobatan';
        }

        if (tombolSimpan) {
            tombolSimpan.disabled = false;
            tombolSimpan.innerHTML =
                '<i class="bi bi-save" aria-hidden="true"></i> Simpan Semua Pengobatan';
        }

        clearValidationErrors();
    }

    // =========================================
    // ADD MODE
    // =========================================
    window.bukaModalTambah = function () {
        if (!modal || !form || !daftarPengobatan) {
            console.error('Elemen modal pengobatan tidak ditemukan.');
            return;
        }

        resetForm();

        // Buat kelompok pertama secara otomatis
        buatKelompokPengobatan();

        openModal(modal);
    };

    // =========================================
    // ADD GROUP
    // =========================================
    if (tombolTambah) {
        tombolTambah.addEventListener('click', function () {
            if (modeForm !== 'tambah' || !daftarPengobatan) return;

            buatKelompokPengobatan();

            // Arahkan tampilan ke kelompok terbaru
            const groups = daftarPengobatan.querySelectorAll(
                '.pengobatan-group'
            );

            const groupTerakhir = groups[groups.length - 1];

            if (groupTerakhir) {
                groupTerakhir.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    }

    // =========================================
    // EDIT MODE
    // =========================================
    window.bukaModalEdit = function (data) {
        if (!modal || !form || !daftarPengobatan) {
            console.error('Elemen modal pengobatan tidak ditemukan.');
            return;
        }

        // Mendukung data berupa object maupun JSON string.
        if (typeof data === 'string') {
            try {
                data = JSON.parse(data);
            } catch (error) {
                console.error(
                    'Data pengobatan untuk edit tidak valid.',
                    error
                );
                return;
            }
        }

        if (!data || !data.id_pengobatan) {
            console.error('ID pengobatan tidak ditemukan.');
            return;
        }

        resetForm();
        modeForm = 'edit';

        if (modalTitle) {
            modalTitle.textContent = 'Edit Pengobatan';
        }

        // Ubah URL menjadi URL update
        const updateUrl = form.dataset.updateUrl || '';

        if (!updateUrl) {
            console.error(
                'URL update pengobatan belum diatur pada data-update-url.'
            );
            return;
        }

        form.action =
            updateUrl.replace(/\/$/, '') + '/' +
            encodeURIComponent(data.id_pengobatan);

        // Isi dropdown penyakit
        const idEntri = document.getElementById('id_entri');

        if (idEntri) {
            idEntri.value = data.id_entri == null
                ? ''
                : String(data.id_entri);
        }

        // Sembunyikan tombol tambah kelompok
        if (tombolTambah) {
            tombolTambah.style.display = 'none';
        }

        // Buat satu kelompok dengan data lama
        buatKelompokPengobatan(data);

        if (tombolSimpan) {
            tombolSimpan.innerHTML =
                '<i class="bi bi-save" aria-hidden="true"></i> Simpan Perubahan';
        }

        openModal(modal);
    };

    // =========================================
    // VALIDATE ALL FIELDS
    // =========================================
    if (form) {
        form.noValidate = true;

        form.addEventListener('submit', function (event) {
            if (sedangMengirim) {
                event.preventDefault();
                return;
            }

            clearValidationErrors();

            const messages = [];
            let firstInvalid = null;

            // Validasi pilihan penyakit
            const idEntri = document.getElementById('id_entri');

            if (idEntri && !idEntri.value) {
                idEntri.classList.add('is-invalid');
                idEntri.setAttribute('aria-invalid', 'true');

                messages.push(
                    'Pilih nama penyakit terlebih dahulu.'
                );

                firstInvalid = idEntri;
            }

            // Validasi kelompok pengobatan
            const groups = daftarPengobatan
                ? Array.from(
                    daftarPengobatan.querySelectorAll('.pengobatan-group')
                )
                : [];

            if (groups.length === 0) {
                messages.push(
                    'Tambahkan minimal satu kelompok pengobatan.'
                );
            }

            const labels = {
                jenis: 'Jenis Pengobatan',
                bahan_asli: 'Bahan Asli',
                bagian_tubuh: 'Bagian Tubuh',
                cara: 'Cara Pengobatan'
            };

            groups.forEach(function (group, groupIndex) {
                Object.keys(labels).forEach(function (fieldName) {
                    const input = group.querySelector(
                        '[data-field="' + fieldName + '"]'
                    );

                    if (!input) {
                        messages.push(
                            'Pengobatan ' + (groupIndex + 1) +
                            ': field ' + labels[fieldName] +
                            ' tidak ditemukan.'
                        );
                        return;
                    }

                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        input.setAttribute('aria-invalid', 'true');

                        messages.push(
                            'Pengobatan ' + (groupIndex + 1) +
                            ': ' + labels[fieldName] +
                            ' wajib diisi.'
                        );

                        if (!firstInvalid) {
                            firstInvalid = input;
                        }
                    }
                });
            });

            if (messages.length > 0) {
                event.preventDefault();
                showValidationErrors(messages, firstInvalid);
                return;
            }

            // Cegah pengiriman ganda ketika tombol ditekan berulang kali.
            sedangMengirim = true;

            if (tombolSimpan) {
                tombolSimpan.disabled = true;
                tombolSimpan.textContent = 'Menyimpan...';
            }
        });
    }

    // =========================================
    // CLOSE ADD / EDIT MODAL
    // =========================================
    if (modal) {
        modal.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(modal);
            });
        });
    }

    // =========================================
    // DELETE MODE
    // =========================================
    window.bukaModalHapus = function (id, nama) {
        if (!deleteModal || !deleteForm) {
            console.error('Modal hapus pengobatan tidak ditemukan.');
            return;
        }

        if (id == null || id === '') {
            console.error('ID pengobatan untuk dihapus tidak valid.');
            return;
        }

        const deleteUrl = deleteForm.dataset.deleteUrl || '';

        if (!deleteUrl) {
            console.error(
                'URL hapus pengobatan belum diatur pada data-delete-url.'
            );
            return;
        }

        deleteForm.action =
            deleteUrl.replace(/\/$/, '') + '/' +
            encodeURIComponent(id);

        const namaHapus = document.getElementById(
            'namaPengobatanHapus'
        );

        if (namaHapus) {
            namaHapus.textContent = nama || 'data pengobatan';
        }

        openModal(deleteModal);
    };

    // =========================================
    // CLOSE DELETE MODAL
    // =========================================
    if (deleteModal) {
        deleteModal.querySelectorAll('[data-close-delete]').forEach(
            function (button) {
                button.addEventListener('click', function () {
                    closeModal(deleteModal);
                });
            }
        );
    }

    // =========================================
    // CLOSE MODAL WITH ESCAPE
    // =========================================
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        if (modal && modal.classList.contains('is-open')) {
            closeModal(modal);
        }

        if (deleteModal && deleteModal.classList.contains('is-open')) {
            closeModal(deleteModal);
        }
    });

    // =========================================
    // TOAST NOTIFICATION
    // =========================================
    const toast = document.getElementById('glToast');
    const toastClose = document.getElementById('glToastClose');

    if (toast) {
        let toastTimer;

        function closeToast() {
            if (!toast.isConnected) return;

            toast.classList.add('gl-toast-out');

            window.setTimeout(function () {
                if (toast.isConnected) {
                    toast.remove();
                }
            }, 300);
        }

        toastTimer = window.setTimeout(closeToast, 4000);

        if (toastClose) {
            toastClose.addEventListener('click', function () {
                window.clearTimeout(toastTimer);
                closeToast();
            });
        }
    }

    console.log('JavaScript Pengobatan berhasil dijalankan.');
});