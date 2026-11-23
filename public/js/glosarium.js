(() => {
    const $  = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    const q        = $('#gl-q');
    const clearBtn = $('#gl-clear');
    const entries  = $$('.gl-entry');
    const groups   = $$('.gl-group');
    const azLinks  = $$('#gl-az a');
    const resText  = $('#gl-result-text');
    const resetBtn = $('#gl-reset');
    const empty    = $('#gl-empty');
    const topBtn   = $('#gl-top');
    const total    = entries.length;

    const state = { keyword: '', kategori: '', bahasa: '' };
    const init  = window.GLOSARIUM_INIT || {};

    const norm = (s) => s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();

    // data-cari juga dinormalisasi agar "sengel" menemukan "Séngél"
    entries.forEach((e) => { e.dataset.cariNorm = norm(e.dataset.cari || ''); });

    function apply(pushUrl = true) {
        const kw = norm(state.keyword);
        const words = kw ? kw.split(/\s+/) : [];
        let shown = 0;

        entries.forEach((e) => {
            const ok =
                (!state.kategori || e.dataset.kat === state.kategori) &&
                (!state.bahasa   || e.dataset.bhs === state.bahasa) &&
                words.every((w) => e.dataset.cariNorm.includes(w));
            e.hidden = !ok;
            if (ok) shown++;
        });

        groups.forEach((g) => {
            const n = $$('.gl-entry:not([hidden])', g).length;
            g.hidden = n === 0;
            const c = $('.gl-group__count b', g);
            if (c) c.textContent = n;
        });

        azLinks.forEach((a) => {
            const g = $('#huruf-' + a.dataset.huruf);
            a.classList.toggle('is-off', !g || g.hidden);
        });

        const filtered = !!(state.keyword || state.kategori || state.bahasa);
        resText.innerHTML = filtered
            ? `Menemukan <strong>${shown}</strong> dari ${total} istilah`
            : `Menampilkan <strong>${total}</strong> istilah`;
        resetBtn.hidden = !filtered;
        clearBtn.hidden = !state.keyword;
        empty.hidden = shown !== 0;

        if (pushUrl) {
            const p = new URLSearchParams();
            if (state.keyword)  p.set('keyword', state.keyword);
            if (state.kategori) p.set('kategori', state.kategori);
            if (state.bahasa)   p.set('bahasa', state.bahasa);
            const qs = p.toString();
            history.replaceState(null, '', location.pathname + (qs ? '?' + qs : '') + location.hash);
        }
    }

    function setActive(groupSel, attr, value) {
        $$(groupSel + ' button').forEach((b) => b.classList.toggle('is-active', (b.dataset[attr] || '') === value));
    }

    function reset() {
        state.keyword = state.kategori = state.bahasa = '';
        q.value = '';
        setActive('#gl-kategori', 'kat', '');
        setActive('#gl-bahasa', 'bhs', '');
        apply();
        q.focus();
    }

    // --- events ---
    let t;
    q.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(() => { state.keyword = q.value; apply(); }, 120);
    });
    clearBtn.addEventListener('click', () => { q.value = ''; state.keyword = ''; apply(); q.focus(); });

    $('#gl-kategori').addEventListener('click', (e) => {
        const b = e.target.closest('button'); if (!b) return;
        state.kategori = b.dataset.kat || '';
        setActive('#gl-kategori', 'kat', state.kategori);
        apply();
    });
    $('#gl-bahasa').addEventListener('click', (e) => {
        const b = e.target.closest('button'); if (!b) return;
        state.bahasa = b.dataset.bhs || '';
        setActive('#gl-bahasa', 'bhs', state.bahasa);
        apply();
    });
    resetBtn.addEventListener('click', reset);
    $('#gl-empty-reset').addEventListener('click', reset);

    // --- A-Z: tandai huruf yang sedang dilihat + tombol ke atas ---
    const io = 'IntersectionObserver' in window
        ? new IntersectionObserver((list) => {
            list.forEach((en) => {
                if (!en.isIntersecting) return;
                azLinks.forEach((a) => a.classList.toggle('is-current', a.dataset.huruf === en.target.dataset.huruf));
            });
        }, { rootMargin: '-35% 0px -60% 0px' })
        : null;
    if (io) groups.forEach((g) => io.observe(g));

    window.addEventListener('scroll', () => { topBtn.hidden = window.scrollY < 600; }, { passive: true });
    topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // --- state awal dari URL ---
    state.keyword  = init.keyword  || '';
    state.kategori = init.kategori || '';
    state.bahasa   = init.bahasa   || '';
    q.value = state.keyword;
    setActive('#gl-kategori', 'kat', state.kategori);
    setActive('#gl-bahasa', 'bhs', state.bahasa);
    apply(false);
})();
