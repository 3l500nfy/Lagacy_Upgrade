// Modern Dialog JavaScript
class ModernDialog {
    constructor(options = {}) {
        this.options = {
            title: 'Dialog',
            message: '',
            type: 'info', // info, warning, success, danger
            buttons: [],
            onClose: null,
            ...options
        };
        this.dialog = null;
        this.overlay = null;
    }

    show() {
        this.createDialog();
        document.body.appendChild(this.overlay);
        document.body.style.overflow = 'hidden';
        
        // Focus management
        setTimeout(() => {
            const firstButton = this.dialog.querySelector('.modern-dialog-btn');
            if (firstButton) firstButton.focus();
        }, 100);
    }

    hide() {
        if (this.dialog && this.overlay) {
            this.dialog.classList.add('closing');
            this.overlay.classList.add('closing');
            
            setTimeout(() => {
                if (this.overlay.parentNode) {
                    this.overlay.parentNode.removeChild(this.overlay);
                }
                document.body.style.overflow = '';
                if (this.options.onClose) {
                    this.options.onClose();
                }
            }, 200);
        }
    }

    createDialog() {
        // Create overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'modern-dialog-overlay';
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.hide();
            }
        });

        // Create dialog
        this.dialog = document.createElement('div');
        this.dialog.className = 'modern-dialog';

        // Create header
        const header = document.createElement('div');
        header.className = 'modern-dialog-header';
        
        const title = document.createElement('h3');
        title.textContent = this.options.title;
        header.appendChild(title);

        const closeBtn = document.createElement('button');
        closeBtn.className = 'modern-dialog-close';
        closeBtn.textContent = 'X';
        closeBtn.addEventListener('click', () => this.hide());
        header.appendChild(closeBtn);

        // Create content
        const content = document.createElement('div');
        content.className = 'modern-dialog-content';

        // Add icon if type is specified
        if (this.options.type && this.options.type !== 'info') {
            const icon = document.createElement('div');
            icon.className = `modern-dialog-icon ${this.options.type}`;
            
            const iconSymbol = this.getIconSymbol(this.options.type);
            icon.innerHTML = iconSymbol;
            content.appendChild(icon);
        }

        const message = document.createElement('h4');
        message.textContent = this.options.message;
        content.appendChild(message);

        // Create buttons
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'modern-dialog-buttons';

        this.options.buttons.forEach(button => {
            const btn = document.createElement('button');
            btn.className = `modern-dialog-btn modern-dialog-btn-${button.type || 'secondary'}`;
            btn.textContent = button.text;
            btn.addEventListener('click', () => {
                if (button.onClick) {
                    button.onClick();
                }
                this.hide();
            });
            buttonContainer.appendChild(btn);
        });

        // Assemble dialog
        this.dialog.appendChild(header);
        this.dialog.appendChild(content);
        this.dialog.appendChild(buttonContainer);
        this.overlay.appendChild(this.dialog);

        // Keyboard support
        this.setupKeyboardSupport();
    }

    getIconSymbol(type) {
        const icons = {
            warning: '!',
            success: 'OK',
            danger: 'X',
            info: 'i'
        };
        return icons[type] || icons.info;
    }

    setupKeyboardSupport() {
        const handleKeydown = (e) => {
            if (e.key === 'Escape') {
                this.hide();
                document.removeEventListener('keydown', handleKeydown);
            }
        };
        document.addEventListener('keydown', handleKeydown);
    }
}

// Convenience functions for common dialog types
window.showModernDialog = {
    confirm: (message, onConfirm, onCancel) => {
        const dialog = new ModernDialog({
            title: 'Computer Based Exams',
            message: message,
            type: 'warning',
            buttons: [
                {
                    text: 'Yes',
                    type: 'danger',
                    onClick: onConfirm
                },
                {
                    text: 'No',
                    type: 'secondary',
                    onClick: onCancel
                }
            ]
        });
        dialog.show();
        return dialog;
    },

    alert: (message, onOk) => {
        const dialog = new ModernDialog({
            title: 'Computer Based Exams',
            message: message,
            type: 'info',
            buttons: [
                {
                    text: 'OK',
                    type: 'primary',
                    onClick: onOk
                }
            ]
        });
        dialog.show();
        return dialog;
    },

    success: (message, onOk) => {
        const dialog = new ModernDialog({
            title: 'Computer Based Exams',
            message: message,
            type: 'success',
            buttons: [
                {
                    text: 'OK',
                    type: 'primary',
                    onClick: onOk
                }
            ]
        });
        dialog.show();
        return dialog;
    }
};
