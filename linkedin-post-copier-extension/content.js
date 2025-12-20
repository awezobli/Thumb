(() => {
  const BUTTON_ATTR = "data-lipc-copy-btn";
  const ROOT_ATTR = "data-lipc-processed";

  function normalizeText(text) {
    return String(text || "")
      .replace(/\u00a0/g, " ")
      .replace(/[ \t]+\n/g, "\n")
      .replace(/\n{3,}/g, "\n\n")
      .trim();
  }

  function getPostText(postEl) {
    // Heurystyki: LinkedIn często zmienia klasy. Szukamy najpierw znanych kontenerów tekstu.
    const candidates = [
      ".feed-shared-update-v2__description",
      ".update-components-text",
      ".feed-shared-inline-show-more-text",
      ".feed-shared-text",
      "div[data-test-id='main-feed-activity-card__commentary']",
      "span[dir='ltr']"
    ];

    for (const sel of candidates) {
      const el = postEl.querySelector(sel);
      const t = normalizeText(el?.innerText);
      if (t) return t;
    }

    // Fallback: bierzemy widoczny tekst z posta i próbujemy go „odszumić”.
    const raw = normalizeText(postEl.innerText);
    if (!raw) return "";

    // Usuń typowe etykiety UI, które potrafią się wkleić wraz z tekstem.
    const lines = raw
      .split("\n")
      .map((l) => l.trim())
      .filter(Boolean)
      .filter(
        (l) =>
          !/^(Like|Comment|Repost|Send|Share|More|Follow|Connect|Message|React|Reply)$/i.test(
            l
          )
      );

    return normalizeText(lines.join("\n"));
  }

  async function copyToClipboard(text) {
    const value = normalizeText(text);
    if (!value) throw new Error("Brak treści do skopiowania.");

    // Najpierw nowoczesne API.
    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(value);
        return;
      }
    } catch {
      // przechodzimy do fallbacku
    }

    // Fallback: execCommand (działa w większości przypadków przy akcji użytkownika).
    const ta = document.createElement("textarea");
    ta.value = value;
    ta.setAttribute("readonly", "");
    ta.style.position = "fixed";
    ta.style.top = "-1000px";
    ta.style.left = "-1000px";
    document.body.appendChild(ta);
    ta.select();
    ta.setSelectionRange(0, ta.value.length);
    const ok = document.execCommand("copy");
    document.body.removeChild(ta);
    if (!ok) throw new Error("Nie udało się skopiować do schowka.");
  }

  function setButtonState(btn, state, message) {
    btn.dataset.lipcState = state;
    if (message) btn.title = message;
    if (state === "copied") btn.textContent = "Skopiowano";
    else if (state === "error") btn.textContent = "Błąd";
    else btn.textContent = "Kopiuj";
  }

  function ensureButton(postEl) {
    if (postEl.hasAttribute(ROOT_ATTR)) return;
    postEl.setAttribute(ROOT_ATTR, "1");

    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "lipc-copy-btn";
    btn.setAttribute(BUTTON_ATTR, "1");
    btn.textContent = "Kopiuj";
    btn.title = "Kopiuj treść posta do schowka";

    btn.addEventListener("click", async (e) => {
      e.preventDefault();
      e.stopPropagation();

      setButtonState(btn, "busy", "Kopiowanie…");
      btn.disabled = true;
      try {
        const text = getPostText(postEl);
        await copyToClipboard(text);
        setButtonState(btn, "copied", "Skopiowano do schowka");
        setTimeout(() => setButtonState(btn, "idle"), 1200);
      } catch (err) {
        setButtonState(btn, "error", err?.message || "Błąd kopiowania");
        setTimeout(() => setButtonState(btn, "idle"), 1500);
      } finally {
        btn.disabled = false;
      }
    });

    // Wpinamy przycisk w prawy górny róg posta.
    // Uwaga: postEl musi mieć pozycjonowanie względem przycisku.
    postEl.classList.add("lipc-root");
    postEl.appendChild(btn);
  }

  function findPostRoots() {
    // LinkedIn używa <article> dla kart w feedzie, ale bywają też inne kontenery.
    const selectors = [
      "div.feed-shared-update-v2",
      "article",
      "div[data-urn][data-urn*='urn:li:activity:']"
    ];

    const els = [];
    for (const sel of selectors) {
      document.querySelectorAll(sel).forEach((el) => {
        // Filtr: zbyt małe elementy / brak tekstu raczej nie są postami
        if (!(el instanceof HTMLElement)) return;
        if (el.offsetHeight < 80) return;
        els.push(el);
      });
    }

    // Unikalne
    return Array.from(new Set(els));
  }

  function scan() {
    for (const postEl of findPostRoots()) ensureButton(postEl);
  }

  // Start + obserwacja dynamicznego feeda
  scan();
  const mo = new MutationObserver(() => scan());
  mo.observe(document.documentElement, { childList: true, subtree: true });
})();

