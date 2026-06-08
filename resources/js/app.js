import './bootstrap'

// ── Pagination links — hapus default Tailwind style dari Laravel ───────────
document.addEventListener('DOMContentLoaded', () => {

    // Auto-dismiss flash messages setelah 5 detik
    const alerts = document.querySelectorAll('.alert')
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s, transform 0.5s'
            alert.style.opacity = '0'
            alert.style.transform = 'translateY(-8px)'
            setTimeout(() => alert.remove(), 500)
        }, 5000)
    })

    // Confirm dialog yang lebih baik — gunakan data-confirm attribute
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        const msg = form.getAttribute('onsubmit')?.match(/confirm\('([^']+)'\)/)?.[1]
        if (!msg) return

        form.removeAttribute('onsubmit')
        form.addEventListener('submit', (e) => {
            e.preventDefault()
            if (window.confirm(msg)) form.submit()
        })
    })

    // Aktifkan loading state pada semua btn-primary saat form submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type=submit].btn-primary')
            if (btn && !btn.disabled) {
                btn.setAttribute('aria-busy', 'true')
                btn.disabled = true
                // Restore setelah 10s sebagai fallback
                setTimeout(() => {
                    btn.removeAttribute('aria-busy')
                    btn.disabled = false
                }, 10000)
            }
        })
    })
})
