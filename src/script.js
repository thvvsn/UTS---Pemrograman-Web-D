document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    const navToggle = document.querySelector('.nav-toggle');
    const primaryNav = document.querySelector('.primary-nav');

    if (navToggle && primaryNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = primaryNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('href');
            if (!targetId || targetId === '#') {
                return;
            }

            const targetEl = document.querySelector(targetId);
            if (!targetEl) {
                return;
            }

            event.preventDefault();

            const headerOffset = header ? header.offsetHeight + 20 : 100;
            const targetPosition = targetEl.getBoundingClientRect().top + window.scrollY - headerOffset;

            window.scrollTo({
                top: Math.max(0, targetPosition),
                behavior: 'smooth',
            });

            if (primaryNav && primaryNav.classList.contains('is-open')) {
                primaryNav.classList.remove('is-open');
                if (navToggle) {
                    navToggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });

    const flashContainer = document.querySelector('.flash-container');
    const flashClose = document.querySelector('.flash-close');
    let autoDismissTimer = null;
    let fadeOutTimer = null;

    if (flashClose) {
        flashClose.addEventListener('click', () => {
            if (autoDismissTimer !== null) {
                clearTimeout(autoDismissTimer);
            }
            if (fadeOutTimer !== null) {
                clearTimeout(fadeOutTimer);
            }
            const container = flashClose.closest('.flash-container');
            if (container) {
                container.remove();
            }
        });
    }

    if (flashContainer && flashContainer.querySelector('.flash-success')) {
        autoDismissTimer = setTimeout(() => {
            flashContainer.classList.add('is-fading-out');
            fadeOutTimer = setTimeout(() => {
                if (flashContainer.isConnected) {
                    flashContainer.remove();
                }
            }, 400);
        }, 5000);
    }

    const searchInput = document.getElementById('catalog-search');
    const categorySelect = document.getElementById('catalog-category');
    const ebookCards = document.querySelectorAll('.ebook-card');
    const emptyState = document.getElementById('catalog-empty');

    const filterCatalog = () => {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const selectedCategory = categorySelect ? categorySelect.value : 'all';
        let visibleCount = 0;

        ebookCards.forEach((card) => {
            const title = card.dataset.title || '';
            const author = card.dataset.author || '';
            const isbn = card.dataset.isbn || '';
            const category = card.dataset.category || '';

            const matchesQuery =
                query === '' ||
                title.includes(query) ||
                author.includes(query) ||
                isbn.includes(query);

            const matchesCategory =
                selectedCategory === 'all' || category === selectedCategory;

            const shouldShow = matchesQuery && matchesCategory;
            card.style.display = shouldShow ? 'flex' : 'none';
            if (shouldShow) {
                visibleCount++;
            }
        });

        if (emptyState) {
            emptyState.hidden = visibleCount > 0;
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', filterCatalog);
    }
    if (categorySelect) {
        categorySelect.addEventListener('change', filterCatalog);
    }

    const selectEbookButtons = document.querySelectorAll('.btn-select-ebook');
    const borrowEbookSelect = document.getElementById('borrow_ebook_id');
    const borrowSection = document.getElementById('borrow');

    selectEbookButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const ebookId = button.dataset.ebookId;
            if (borrowEbookSelect && ebookId) {
                borrowEbookSelect.value = ebookId;
                clearFieldError(borrowEbookSelect, 'error-borrow_ebook_id');
            }
            if (borrowSection) {
                const headerOffset = header ? header.offsetHeight + 20 : 100;
                const targetPosition =
                    borrowSection.getBoundingClientRect().top + window.scrollY - headerOffset;
                window.scrollTo({
                    top: Math.max(0, targetPosition),
                    behavior: 'smooth',
                });
            }
        });
    });

    function setFieldError(inputEl, errorId, message) {
        const errorEl = document.getElementById(errorId);
        if (inputEl) {
            inputEl.classList.add('input-invalid');
        }
        if (errorEl) {
            errorEl.textContent = message;
        }
    }

    function clearFieldError(inputEl, errorId) {
        const errorEl = document.getElementById(errorId);
        if (inputEl) {
            inputEl.classList.remove('input-invalid');
        }
        if (errorEl) {
            errorEl.textContent = '';
        }
    }

    const memberForm = document.getElementById('member-form');
    if (memberForm) {
        const formTitle = document.getElementById('member-form-title');
        const formSubtitle = document.getElementById('member-form-subtitle');
        const actionInput = document.getElementById('member_action');
        const userIdInput = document.getElementById('member_user_id');
        const fullNameInput = document.getElementById('full_name');
        const emailInput = document.getElementById('email');
        const tierSelect = document.getElementById('membership_tier');
        const submitBtn = document.getElementById('member-submit-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-member');
        const editButtons = document.querySelectorAll('.btn-edit-member');
        const membersSection = document.getElementById('members');

        const resetMemberFormToRegister = () => {
            if (formTitle) {
                formTitle.textContent = 'Daftar Anggota Baru';
            }
            if (formSubtitle) {
                formSubtitle.textContent = 'Buat profil anggota untuk mulai meminjam judul digital.';
            }
            if (actionInput) {
                actionInput.value = 'register_user';
            }
            if (userIdInput) {
                userIdInput.value = '';
            }
            if (fullNameInput) {
                fullNameInput.value = '';
            }
            if (emailInput) {
                emailInput.value = '';
            }
            if (tierSelect) {
                tierSelect.value = 'Standard';
            }
            if (submitBtn) {
                submitBtn.textContent = 'Daftarkan Anggota';
            }
            if (cancelEditBtn) {
                cancelEditBtn.classList.add('is-hidden');
            }
            clearFieldError(fullNameInput, 'error-full_name');
            clearFieldError(emailInput, 'error-email');
            clearFieldError(tierSelect, 'error-membership_tier');
        };

        editButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                if (formTitle) {
                    formTitle.textContent = 'Edit Anggota';
                }
                if (formSubtitle) {
                    formSubtitle.textContent = 'Perbarui nama, alamat email, atau paket keanggotaan anggota.';
                }
                if (actionInput) {
                    actionInput.value = 'update_member';
                }
                if (userIdInput) {
                    userIdInput.value = button.dataset.userId || '';
                }
                if (fullNameInput) {
                    fullNameInput.value = button.dataset.fullName || '';
                }
                if (emailInput) {
                    emailInput.value = button.dataset.email || '';
                }
                if (tierSelect) {
                    tierSelect.value = button.dataset.tier || 'Standard';
                }
                if (submitBtn) {
                    submitBtn.textContent = 'Simpan Perubahan';
                }
                if (cancelEditBtn) {
                    cancelEditBtn.classList.remove('is-hidden');
                }
                clearFieldError(fullNameInput, 'error-full_name');
                clearFieldError(emailInput, 'error-email');
                clearFieldError(tierSelect, 'error-membership_tier');

                if (membersSection) {
                    const headerOffset = header ? header.offsetHeight + 20 : 100;
                    const targetPosition =
                        membersSection.getBoundingClientRect().top + window.scrollY - headerOffset;
                    window.scrollTo({
                        top: Math.max(0, targetPosition),
                        behavior: 'smooth',
                    });
                }
                if (fullNameInput) {
                    fullNameInput.focus();
                }
            });
        });

        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', (event) => {
                event.preventDefault();
                resetMemberFormToRegister();
            });
        }

        [
            [fullNameInput, 'error-full_name'],
            [emailInput, 'error-email'],
            [tierSelect, 'error-membership_tier'],
        ].forEach(([el, errId]) => {
            if (el) {
                el.addEventListener('input', () => clearFieldError(el, errId));
                el.addEventListener('change', () => clearFieldError(el, errId));
            }
        });

        memberForm.addEventListener('submit', (event) => {
            let isValid = true;
            const fullName = fullNameInput ? fullNameInput.value.trim() : '';
            const email = emailInput ? emailInput.value.trim() : '';
            const tier = tierSelect ? tierSelect.value : '';

            if (fullName.length < 2) {
                setFieldError(fullNameInput, 'error-full_name', 'Harap masukkan nama lengkap (minimal 2 karakter).');
                isValid = false;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                setFieldError(emailInput, 'error-email', 'Harap masukkan alamat email yang valid.');
                isValid = false;
            }

            if (!['Standard', 'Student', 'Premium'].includes(tier)) {
                setFieldError(tierSelect, 'error-membership_tier', 'Harap pilih paket keanggotaan yang valid.');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    const borrowForm = document.getElementById('borrow-form');
    if (borrowForm) {
        const userSelect = document.getElementById('borrow_user_id');
        const ebookSelect = document.getElementById('borrow_ebook_id');
        const daysInput = document.getElementById('rental_days');

        [
            [userSelect, 'error-borrow_user_id'],
            [ebookSelect, 'error-borrow_ebook_id'],
            [daysInput, 'error-rental_days'],
        ].forEach(([el, errId]) => {
            if (el) {
                el.addEventListener('input', () => clearFieldError(el, errId));
                el.addEventListener('change', () => clearFieldError(el, errId));
            }
        });

        borrowForm.addEventListener('submit', (event) => {
            let isValid = true;
            const userId = userSelect ? userSelect.value.trim() : '';
            const ebookId = ebookSelect ? ebookSelect.value.trim() : '';
            const days = daysInput ? Number(daysInput.value) : 0;

            if (userId === '') {
                setFieldError(userSelect, 'error-borrow_user_id', 'Harap pilih anggota yang terdaftar.');
                isValid = false;
            }

            if (ebookId === '') {
                setFieldError(ebookSelect, 'error-borrow_ebook_id', 'Harap pilih e-book yang tersedia.');
                isValid = false;
            }

            if (!Number.isInteger(days) || days < 1 || days > 30) {
                setFieldError(daysInput, 'error-rental_days', 'Durasi sewa harus antara 1 hingga 30 hari.');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});

