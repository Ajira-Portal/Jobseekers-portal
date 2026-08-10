import swCommon from './i18n/sw/common';
import swWelcome from './i18n/sw/welcome';
import swForms from './i18n/sw/forms';
import enCommon from './i18n/en/common';
import enWelcome from './i18n/en/welcome';
import enForms from './i18n/en/forms';

const DICTIONARIES = {
  sw: {
    ...swCommon,
    ...swWelcome,
    ...swForms,
  },
  en: {
    ...enCommon,
    ...enWelcome,
    ...enForms,
  },
};

function activeLocale() {
  const lang = (document.documentElement.lang || '').toLowerCase();
  if (lang.startsWith('en')) {
    return 'en';
  }
  return 'sw';
}

function preserveSpacing(original, translated) {
  const lead = original.match(/^\s*/)?.[0] || '';
  const tail = original.match(/\s*$/)?.[0] || '';
  return `${lead}${translated}${tail}`;
}

function translateValue(raw, dict) {
  if (!raw) {
    return raw;
  }

  const key = raw.trim();
  if (!key) {
    return raw;
  }

  const hit = dict[key] || dict[key.toLowerCase()];
  if (!hit) {
    return raw;
  }

  return preserveSpacing(raw, hit);
}

function walkText(root, dict) {
  const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
  const skip = new Set(['SCRIPT', 'STYLE', 'NOSCRIPT', 'TEXTAREA', 'CODE', 'PRE']);

  let node = walker.nextNode();
  while (node) {
    const parent = node.parentElement ? node.parentElement.tagName : '';
    if (!skip.has(parent)) {
      const before = node.nodeValue || '';
      const after = translateValue(before, dict);
      if (after !== before) {
        node.nodeValue = after;
      }
    }
    node = walker.nextNode();
  }
}

function translateAttrs(root, dict) {
  const attrs = ['placeholder', 'title', 'aria-label', 'value'];

  attrs.forEach((attr) => {
    root.querySelectorAll(`[${attr}]`).forEach((el) => {
      if (attr === 'value') {
        const isActionInput = el.tagName === 'INPUT' && ['submit', 'button', 'reset'].includes((el.getAttribute('type') || '').toLowerCase());
        if (!isActionInput) {
          return;
        }
      }

      const before = el.getAttribute(attr) || '';
      const after = translateValue(before, dict);
      if (after !== before) {
        el.setAttribute(attr, after.trim());
      }
    });
  });

  root.querySelectorAll('option').forEach((opt) => {
    const before = opt.textContent || '';
    const after = translateValue(before, dict);
    if (after !== before) {
      opt.textContent = after;
    }
  });
}

function applyTranslationsTo(root) {
  const locale = activeLocale();
  const dict = DICTIONARIES[locale] || DICTIONARIES.sw;
  walkText(root, dict);
  translateAttrs(root, dict);
}

function observe() {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === Node.ELEMENT_NODE) {
          applyTranslationsTo(node);
        }
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
}

window.__t = function __t(text) {
  const locale = activeLocale();
  const dict = DICTIONARIES[locale] || DICTIONARIES.sw;
  return (translateValue(String(text || ''), dict) || '').trim();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    applyTranslationsTo(document.body);
    observe();
  });
} else {
  applyTranslationsTo(document.body);
  observe();
}
