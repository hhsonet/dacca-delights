<script type="text/x-dc" data-dc-script data-props="{&quot;$preview&quot;:{&quot;width&quot;:420},&quot;pickupZone&quot;:{&quot;editor&quot;:&quot;text&quot;,&quot;default&quot;:&quot;North Kafrul&quot;,&quot;tsType&quot;:&quot;string&quot;,&quot;section&quot;:&quot;Fulfilment&quot;},&quot;sameDayCutoffHour&quot;:{&quot;editor&quot;:&quot;int&quot;,&quot;default&quot;:9,&quot;tsType&quot;:&quot;number&quot;,&quot;min&quot;:0,&quot;max&quot;:23,&quot;step&quot;:1,&quot;unit&quot;:&quot;:00&quot;,&quot;section&quot;:&quot;Fulfilment&quot;},&quot;bookingWindowDays&quot;:{&quot;editor&quot;:&quot;int&quot;,&quot;default&quot;:30,&quot;tsType&quot;:&quot;number&quot;,&quot;min&quot;:7,&quot;max&quot;:90,&quot;step&quot;:1,&quot;unit&quot;:&quot; days&quot;,&quot;section&quot;:&quot;Fulfilment&quot;},&quot;freeDeliveryOver&quot;:{&quot;editor&quot;:&quot;int&quot;,&quot;default&quot;:2000,&quot;tsType&quot;:&quot;number&quot;,&quot;min&quot;:0,&quot;max&quot;:6000,&quot;step&quot;:250,&quot;unit&quot;:&quot;tk&quot;,&quot;section&quot;:&quot;Commerce&quot;}}">
// --- multi-page routing -------------------------------------------------
// Base URL and current route are injected by CI4; nav() turns page keys into
// real URLs instead of setState.
const DD_BASE = "<?= rtrim(base_url(), '/') ?>/";
// CI4 rotates the CSRF token per request, so this is mutable — every auth
// response hands back a fresh one.
const DD_CSRF = { header: "<?= csrf_header() ?>", token: "<?= csrf_hash() ?>" };
const DD_SESSION = {
  authed:   <?= session()->get('customerId') ? 'true' : 'false' ?>,
  name:     "<?= esc(session()->get('customerName') ?? '', 'js') ?>",
  lastName: "<?= esc(session()->get('customerLastName') ?? '', 'js') ?>",
  email:    "<?= esc(session()->get('customerEmail') ?? '', 'js') ?>",
  phone:    "<?= esc(session()->get('customerPhone') ?? '', 'js') ?>"
};
const DD_PATHS = {
  home:"", menu:"menu", product:"product", cart:"cart", checkout:"checkout",
  success:"order/success", account:"account", orderdetail:"account/order",
  auth:"login", bulk:"bulk", about:"about"
};
const DD_DRAFT_KEYS = ["firstName","lastName","house","line1","line2","zip","pickup","zone",
  "localPhone","waSame","waCode","waNumber","mapsUrl","geoStatus","deliveryDate","payment",
  "coupon","couponOk","chatLog","chatOpen"];

function ddUrl(page, extra) {
  extra = extra || {};
  let path = DD_PATHS[page] != null ? DD_PATHS[page] : "";
  if (page === "product") {
    path += "/" + (extra.slug || "");
  } else if (page === "orderdetail") {
    // call sites pass an index; the URL carries the order number
    const o = ORDERS[extra.orderIx || 0];
    path += "/" + (o ? o.no.replace(/^#/, "") : "");
  }
  const qs = [];
  if (page === "menu") {
    if (extra.category) qs.push("category=" + encodeURIComponent(extra.category));
    if (extra.query) qs.push("q=" + encodeURIComponent(extra.query));
  }
  if (page === "account" && extra.accountTab) {
    qs.push("tab=" + encodeURIComponent(extra.accountTab));
  }
  return DD_BASE + path + (qs.length ? "?" + qs.join("&") : "");
}

const IMG = (id, w) => "https://images.unsplash.com/photo-" + id + "?auto=format&fit=crop&w=" + (w || 700) + "&q=70";
const FALLBACK = s => "https://picsum.photos/seed/" + s + "/700/700";
const SHOT = n => "https://www.daccadelights.com/assets/Items/" + n;

const CATS = <?= json_encode($dd['CATS'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

const CAT_META = <?= json_encode($dd['CAT_META'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;


// Key ingredients and energy per 100 gm. Per-item overrides first, category
// defaults as the fallback — in production these are columns on `products`.

const nutritionFor = p => ({ ing: p.ing, kcal: p.kcal });

// Bagels share one minimum-order pool: any mix of single bagels must reach 6.
// Items with their own MOQ sit outside the pool; pre-set bunches are exempt.
const BAGEL_POOL_MOQ = 6;
const ITEM_MOQ = <?= json_encode($dd['ITEM_MOQ'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
const inBagelPool = p => p.cat === "Bagels" && !/bunch/i.test(p.name) && !ITEM_MOQ[p.name];

const slugify = n => n.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
let _id = 0;
const PRODUCTS = <?= json_encode($dd['PRODUCTS'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
// units sold in the trailing 7 days — in production:
// SELECT product_id, SUM(qty) FROM order_items JOIN orders o … WHERE o.created_at >= NOW() - INTERVAL 7 DAY
// Best sellers are computed from real order history (see StorefrontData::featured).
const FEATURED = <?= json_encode($dd['FEATURED'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

const GALLERY = <?= json_encode($dd['GALLERY'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

const TESTIMONIALS = <?= json_encode($dd['TESTIMONIALS'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

const STATUS = {
  "Pending":          { bg:"#F5AD18", fg:"#561530" },
  "Confirmed":        { bg:"#EDE3E6", fg:"#561530" },
  "Preparing":        { bg:"#9E1C60", fg:"#FFFFFF" },
  "Out for Delivery": { bg:"#811844", fg:"#FFFFFF" },
  "Delivered":        { bg:"#17693F", fg:"#FFFFFF" },
  "Cancelled":        { bg:"#B3261E", fg:"#FFFFFF" }
};
const STAGES = ["Order Placed","Confirmed","Preparing","Out for Delivery","Delivered"];
const ORDERS = <?= json_encode($dd['ORDERS'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

const IMG_SET = id => [400, 800, 1200].map(w => IMG(id, w) + " " + w + "w").join(", ");
const SUPPORT = {
  "Breads":                  ["1509440159596-0249088772ff", "1549931319-a545dcf3bc73"],
  "Croissants":              ["1555507036-ab1f4038808a", "1549903072-7e6e0bedb7fb"],
  "Sandwiches & Wraps":      ["1509722747041-616f39b57569", "1553909489-cd47e0907980"],
  "Buns & Rolls":            ["1509365465985-25d11c17e812", "1586444248902-2f64eddc13df"],
  "Bagels":                  ["1517686469429-8bdb88b9f907", "1554118811-1e0d58224f24"],
  "Desserts & Savory Sides": ["1499636136210-6f4ee915583e", "1607958996333-41aef7caefaa"],
  "Tarts & Pastries":        ["1571877227200-a0d98ea607e9", "1569864358642-9d1684040f43"],
  "Add ons":                 ["1556910103-1c02745aae4d", "1495474472287-4d71bcdd2085"]
};

const NO_BREAD_OPTIONS = ["Regular Ciabatta","White Ciabatta","Mini Baguette","Khobus",
  "Bánh Mì (Hoagie Bread)","Muffuletta Sandwich Bread","Simit","Plain Focaccia","Mixed Herb Focaccia"];
const needsBreadOptions = p => p.cat === "Breads" && NO_BREAD_OPTIONS.indexOf(p.name) < 0;
// Items outside Breads that still take the sugar choice
const SUGAR_ONLY = ["Jerusalem Bagel"];
// Items sold as one product with a mandatory filling choice
const FILLINGS = { "Chicken Puff": ["Spicy","Creamy"], "Mini Chicken Puff": ["Spicy","Creamy"] };
const needsFilling = p => !!FILLINGS[p.name];
const needsSugar = p => needsBreadOptions(p) || SUGAR_ONLY.indexOf(p.name) >= 0;
// any item whose mandatory choices must be made on the product page
const needsOptions = p => needsSugar(p) || needsFilling(p);

// Dhaka Metropolitan Police thanas plus the named neighbourhoods we quote
// separately. fee = delivery charge in taka; limited = restricted service.
const ZONES = <?= json_encode($dd['ZONES'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

const COD_ZONES = <?= json_encode($dd['COD_ZONES'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
const COUNTRY_CODES = [
  { code:"+880", label:"+880 Bangladesh" }, { code:"+91", label:"+91 India" },
  { code:"+92", label:"+92 Pakistan" }, { code:"+94", label:"+94 Sri Lanka" },
  { code:"+977", label:"+977 Nepal" }, { code:"+971", label:"+971 UAE" },
  { code:"+966", label:"+966 Saudi Arabia" }, { code:"+974", label:"+974 Qatar" },
  { code:"+965", label:"+965 Kuwait" }, { code:"+968", label:"+968 Oman" },
  { code:"+973", label:"+973 Bahrain" }, { code:"+60", label:"+60 Malaysia" },
  { code:"+65", label:"+65 Singapore" }, { code:"+44", label:"+44 United Kingdom" },
  { code:"+1", label:"+1 USA / Canada" }, { code:"+61", label:"+61 Australia" },
  { code:"+49", label:"+49 Germany" }, { code:"+39", label:"+39 Italy" },
  { code:"+33", label:"+33 France" }, { code:"+81", label:"+81 Japan" },
  { code:"+82", label:"+82 South Korea" }, { code:"+27", label:"+27 South Africa" }
];

class Component extends DCLogic {
  state = { page:"<?= esc($page, 'js') ?>", slug:"<?= esc($slug ?? 'sourdough-bread', 'js') ?>", cart:{}, category:"<?= esc($category ?? 'Best Sellers', 'js') ?>", query:"<?= esc($query ?? '', 'js') ?>", shown:8, searchOpen:false, menuOpen:false,
            qty:1, payment:"", coupon:"", couponOk:false, orderNo:"", paid:"", toast:"", authMode:"<?= esc($authMode ?? 'login', 'js') ?>", authBusy:false,
            orderBusy:false,
            authed: DD_SESSION.authed, customerName: DD_SESSION.name, customerLastName: DD_SESSION.lastName,
            customerEmail: DD_SESSION.email, customerPhone: DD_SESSION.phone,
            accountTab:"<?= esc($accountTab ?? 'Dashboard', 'js') ?>", showPw:false, remember:true, terms:false, orderIx:(function(){ const r = "<?= esc($orderRef ?? '', 'js') ?>"; if (!r) return 0; const i = ORDERS.findIndex(o => o.no.replace(/^#/, "") === r); return i < 0 ? 0 : i; })(), bulkSent:false, err:{},
            form:{ name:"", email:"", phone:"", pw:"", pw2:"" },
            shot:0, sugar:"", bform:"", slice:"", filling:"", notes:"",
            chatOpen:false, chatDraft:"", chatLog:[], chatBusy:false, deliveryDate:"", paidDate:"", invoice:null, exportBusy:false, exportNote:"", idMode:"email", otpVia:"email", phoneStep:false, otpStep:false, otpCode:"", otpDigits:["","","","","",""], otpError:"", resendIn:0, house:"", line1:"", line2:"", zip:"", firstName:"", lastName:"",
            pickup:false, zone:"", localPhone:"", waSame:true, waCode:"+880", waNumber:"", mapsUrl:"", geoStatus:"" };

  componentDidMount() {
    // Cart — localStorage, survives browser restarts.
    try {
      const raw = localStorage.getItem("dd_cart2");
      if (raw) {
        const saved = JSON.parse(raw);
        const cart = {};
        Object.keys(saved).forEach(k => {
          const v = saved[k];
          if (typeof v === "number") cart[k + "||||"] = { id: Number(k), qty: v, sugar:"", form:"", slice:"", filling:"", note:"" };
          else if (v && v.id) cart[k] = v;
        });
        this.setState({ cart });
      }
    } catch (e) {}

    // Checkout draft + chat log — sessionStorage, survives page navigation.
    try {
      const d = JSON.parse(sessionStorage.getItem("dd_draft") || "null");
      if (d) {
        const restore = {};
        DD_DRAFT_KEYS.forEach(k => { if (d[k] !== undefined && d[k] !== null) restore[k] = d[k]; });
        this.setState(restore);
      }
    } catch (e) {}

    // Invoice handed over from checkout — without this /order/success is blank.
    if (this.state.page === "success") {
      try {
        const inv = JSON.parse(sessionStorage.getItem("dd_invoice") || "null");
        if (inv) this.setState({ orderNo: inv.orderNo, paid: inv.paid, paidDate: inv.paidDate, invoice: inv.invoice });
      } catch (e) {}
    }

    if (typeof window !== "undefined") {
      this._save = () => this.saveDraft();
      window.addEventListener("beforeunload", this._save);
    }
  }
  componentWillUnmount() {
    clearTimeout(this._t);
    if (typeof window !== "undefined" && this._save) window.removeEventListener("beforeunload", this._save);
  }

  persist(cart) { try { localStorage.setItem("dd_cart2", JSON.stringify(cart)); } catch (e) {} }
  flash(msg) {
    this.setState({ toast: msg });
    clearTimeout(this._t);
    this._t = setTimeout(() => this.setState({ toast: "" }), 2200);
  }
  async renderInvoice(type) {
    if (typeof window === "undefined" || !window.html2canvas || !this.invoiceEl) return null;
    const canvas = await window.html2canvas(this.invoiceEl, {
      backgroundColor: "#FFFFFF", scale: Math.min(3, window.devicePixelRatio * 2 || 2),
      useCORS: true, logging: false
    });
    const mime = type === "jpeg" ? "image/jpeg" : "image/png";
    return { canvas, mime, dataUrl: canvas.toDataURL(mime, 0.95) };
  }
  async exportInvoice(type) {
    this.setState({ exportBusy: true, exportNote: "Rendering your invoice…" });
    try {
      const out = await this.renderInvoice(type);
      if (!out) throw new Error("no renderer");
      const a = document.createElement("a");
      a.href = out.dataUrl;
      a.download = "dacca-delights-" + (this.state.orderNo || "invoice") + "." + (type === "jpeg" ? "jpg" : "png");
      document.body.appendChild(a); a.click(); a.remove();
      this.setState({ exportBusy: false, exportNote: "" });
      this.flash("Invoice saved as " + type.toUpperCase());
    } catch (e) {
      this.setState({ exportBusy: false, exportNote: "Could not render the image — use Save as PDF instead." });
    }
  }
  async shareInvoice() {
    const no = this.state.orderNo || "";
    const text = "Dacca Delights invoice " + no + " — " + (this.state.paid || "") + ", delivery " + (this.state.paidDate || "") + ".";
    try {
      const out = await this.renderInvoice("png");
      if (out && navigator.canShare) {
        const blob = await new Promise(r => out.canvas.toBlob(r, "image/png"));
        const file = new File([blob], "invoice-" + no + ".png", { type: "image/png" });
        if (navigator.canShare({ files: [file] })) {
          await navigator.share({ files: [file], title: "Dacca Delights invoice " + no, text });
          return;
        }
      }
      if (navigator.share) { await navigator.share({ title: "Dacca Delights invoice " + no, text }); return; }
      if (navigator.clipboard) { await navigator.clipboard.writeText(text); this.flash("Invoice details copied"); return; }
      window.open("https://wa.me/8801622823269?text=" + encodeURIComponent(text), "_blank");
    } catch (e) {
      if (e && e.name === "AbortError") return;
      this.setState({ exportNote: "Sharing is not available here — save the image and send it manually." });
    }
  }
  nav(page, extra) {
    extra = extra || {};
    // Same-page transitions (bulk form success, account tab switches) stay
    // client-side — only a genuine page change goes back to the server.
    if (page === this.state.page) {
      const reset = page === "auth"
        ? { idMode:"email", otpVia:"email", phoneStep:false, otpStep:false, otpError:"", err:{}, otpDigits:["","","","","",""] }
        : {};
      this.setState(Object.assign({ page, menuOpen:false, searchOpen:false }, reset, extra));
      if (typeof window !== "undefined") window.scrollTo(0, 0);
      return;
    }
    this.bridge(page, extra);
    if (typeof window !== "undefined") window.location.href = ddUrl(page, extra);
  }

  // Carry across a real page load the state that setState alone cannot survive.
  bridge(page, extra) {
    try {
      if (page === "success") {
        sessionStorage.setItem("dd_invoice", JSON.stringify({
          orderNo: extra.orderNo || "", paid: extra.paid || "",
          paidDate: extra.paidDate || "", invoice: extra.invoice || null
        }));
      }
    } catch (e) {}
    this.saveDraft();
  }

  saveDraft() {
    try {
      const s = this.state, d = {};
      DD_DRAFT_KEYS.forEach(k => { d[k] = s[k]; });
      sessionStorage.setItem("dd_draft", JSON.stringify(d));
    } catch (e) {}
  }
  add(id, n, opts) {
    const key = this.cartKey(id, opts);
    const cart = Object.assign({}, this.state.cart);
    const prev = cart[key];
    cart[key] = {
      id, qty: (prev ? prev.qty : 0) + (n || 1),
      sugar: (opts && opts.sugar) || "", form: (opts && opts.form) || "",
      slice: (opts && opts.slice) || "", filling: (opts && opts.filling) || "",
      note: (opts && opts.note) || (prev ? prev.note : "")
    };
    this.setState({ cart });
    this.persist(cart);
    this.flash(PRODUCTS.find(x => x.id === id).name + " added to cart");
  }
  cartKey(id, o) {
    return [id, (o && o.sugar) || "", (o && o.form) || "", (o && o.slice) || "", (o && o.filling) || ""].join("|");
  }
  lineQty(key) { const l = this.state.cart[key]; return l ? l.qty : 0; }
  optionLabel(l) {
    return [l.filling, l.sugar, l.form, l.slice].filter(Boolean).join(" · ");
  }
  setQty(key, q) {
    const cart = Object.assign({}, this.state.cart);
    if (q <= 0) delete cart[key];
    else cart[key] = Object.assign({}, cart[key], { qty: q });
    this.setState({ cart });
    this.persist(cart);
  }
  price(p) { return p.price; }
  money(n) { return Math.round(n).toLocaleString("en-US") + " tk"; }

  badgeFor(p) {
    if (FEATURED.indexOf(p.name) >= 0) return { text:"BEST SELLER", bg:"#561530", fg:"#F5AD18" };
    if (p.isNew) return { text:"NEW", bg:"#9E1C60", fg:"#FFFFFF" };
    if (/Bunch|Combo|Trio/.test(p.name)) return { text:"POPULAR", bg:"#F5AD18", fg:"#561530" };
    return null;
  }
  minQtyFor(p) {
    if (ITEM_MOQ[p.name]) return ITEM_MOQ[p.name];
    if (inBagelPool(p)) return BAGEL_POOL_MOQ;
    return 1;
  }
  openProduct(p) {
    this.nav("product", { slug: p.slug, qty: this.minQtyFor(p), sugar:"", bform:"", slice:"", filling:"", notes:"", shot:0 });
  }
  card(p) {
    const b = this.badgeFor(p);
    return {
      name: p.name, category: p.cat, image: p.image, note: p.note, isNew: p.isNew,
      price: this.money(p.price),
      qty: this.lineQty(this.cartKey(p.id)),
      inCart: !!this.state.cart[this.cartKey(p.id)],
      notInCart: !this.state.cart[this.cartKey(p.id)],
      dec: () => this.setQty(this.cartKey(p.id), this.lineQty(this.cartKey(p.id)) - 1),
      ing: p.ing, kcal: p.kcal + " kcal / 100g",
      badge: b ? b.text : "", badgeBg: b ? b.bg : "transparent", badgeFg: b ? b.fg : "transparent",
      minQty: this.minQtyFor(p),
      addLabel: needsOptions(p) ? "Choose" : "+ Add",
      addGlyph: needsOptions(p) ? "Choose" : "+",
      addSize: needsOptions(p) ? "12px" : "20px",
      addPadX: needsOptions(p) ? "16px" : "0px",
      addAria: needsOptions(p) ? "Choose options for " + p.name : "Add " + p.name + " to cart",
      open: () => this.openProduct(p),
      // mandatory choices can't be made on a card — send the customer to the item page
      add: needsOptions(p)
        ? () => { this.openProduct(p); this.flash("Choose your options for " + p.name); }
        : () => this.add(p.id, this.minQtyFor(p))
    };
  }

  setField(k, v) { this.setState(s => ({ form: Object.assign({}, s.form, { [k]: v }) })); }
  strength(pw) {
    let n = 0;
    if (pw.length >= 8) n++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) n++;
    if (/[0-9]/.test(pw)) n++;
    if (/[^A-Za-z0-9]/.test(pw)) n++;
    if (!pw) return { pct:"0%", label:"—", color:"#75666B" };
    if (n <= 1) return { pct:"25%", label:"Weak", color:"#B3261E" };
    if (n === 2) return { pct:"50%", label:"Fair", color:"#F5AD18" };
    if (n === 3) return { pct:"75%", label:"Good", color:"#9E1C60" };
    return { pct:"100%", label:"Strong", color:"#17693F" };
  }
  otpTargetLabel() {
    const s = this.state;
    const viaPhone = (s.otpVia || (s.phoneStep ? "phone" : "email")) === "phone";
    return viaPhone ? "+880 " + s.form.phone.trim() : s.form.email.trim();
  }
  sendOtp() {
    const code = String(Math.floor(100000 + Math.random() * 899999));
    this.setState({ otpStep:true, otpVia: this.state.phoneStep ? "phone" : "email", otpCode:code, otpDigits:["","","","","",""], otpError:"", resendIn:30 });
    this.flash("Verification code sent to " + this.otpTargetLabel());
    clearInterval(this._otpTimer);
    this._otpTimer = setInterval(() => {
      const left = this.state.resendIn - 1;
      if (left <= 0) { clearInterval(this._otpTimer); this.setState({ resendIn:0 }); }
      else this.setState({ resendIn:left });
    }, 1000);
    setTimeout(() => { if (this.otpRefs && this.otpRefs[0]) this.otpRefs[0].focus(); }, 60);
  }
  setOtpDigit(i, v) {
    const digits = this.state.otpDigits.slice();
    digits[i] = v.replace(/\D/g, "").slice(-1);
    this.setState({ otpDigits:digits, otpError:"" });
    if (digits[i] && this.otpRefs && this.otpRefs[i + 1]) this.otpRefs[i + 1].focus();
  }
  /**
   * Sign up or sign in against the CI4 endpoints. Server-side validation is
   * authoritative; field errors come back keyed to match the form's own `err`
   * object so they render inline exactly like the client-side ones.
   */
  async doAuth() {
    const s = this.state, f = s.form, reg = s.authMode === "register";
    if (s.authBusy) return;
    this.setState({ authBusy: true, err: {} });

    const body = reg
      ? { name: f.name, email: f.email, phone: f.phone, pw: f.pw, pw2: f.pw2, terms: s.terms }
      : { email: f.email, pw: f.pw };

    try {
      const headers = { "Content-Type": "application/json" };
      headers[DD_CSRF.header] = DD_CSRF.token;

      const res = await fetch(DD_BASE + (reg ? "auth/signup" : "auth/login"), {
        method: "POST",
        headers,
        credentials: "same-origin",
        body: JSON.stringify(body)
      });

      const data = await res.json().catch(() => ({}));
      if (data && data.token) DD_CSRF.token = data.token;

      if (res.ok && data.ok) {
        // Never keep the plaintext password in component state.
        this.setState({ authBusy: false, form: Object.assign({}, f, { pw: "", pw2: "" }) });
        try { sessionStorage.removeItem("dd_draft"); } catch (e) {}
        window.location.href = ddUrl("account", {});
        return;
      }

      this.setState({ authBusy: false, err: (data && data.errors) || {} });
      this.flash(res.status === 429
        ? "Too many attempts — please wait a moment"
        : (reg ? "Could not create your account" : "Sign in failed"));
    } catch (e) {
      this.setState({ authBusy: false });
      this.flash("Could not reach the server. Please try again.");
    }
  }
  /**
   * Send the checkout to the server, which re-prices everything from the
   * database and stores the order. The invoice shown afterwards is built from
   * what was actually saved, not from anything computed in the browser.
   */
  async submitOrder() {
    const s = this.state;
    if (s.orderBusy) return;
    this.setState({ orderBusy: true });

    const payload = {
      cart: s.cart,
      firstName: s.firstName, lastName: s.lastName,
      localPhone: s.localPhone, waSame: s.waSame, waCode: s.waCode, waNumber: s.waNumber,
      pickup: s.pickup, zone: s.zone,
      house: s.house, line1: s.line1, line2: s.line2, zip: s.zip, mapsUrl: s.mapsUrl,
      deliveryDate: s.deliveryDate,
      payment: s.payment,
      coupon: s.couponOk ? s.coupon : ""
    };

    try {
      const headers = { "Content-Type": "application/json" };
      headers[DD_CSRF.header] = DD_CSRF.token;

      const res = await fetch(DD_BASE + "order", {
        method: "POST", headers, credentials: "same-origin",
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => ({}));
      if (data && data.token) DD_CSRF.token = data.token;

      if (!res.ok || !data.ok) {
        this.setState({ orderBusy: false });
        this.flash((data.errors && data.errors[0]) || "We could not place your order.");
        return;
      }

      const o = data.order;
      const invoice = {
        issued: o.placedOn, issuedTime: o.placedTime,
        customer: o.customer, phone: o.phone, whatsapp: o.whatsapp,
        address: o.address, hasEmail: false, email: "",
        map: o.mapsUrl, hasMap: !!o.mapsUrl && !o.isPickup,
        method: o.isPickup ? "Self-pickup" : "Local delivery",
        zoneLine: o.zoneName, date: o.deliveryDate,
        payment: o.paymentLabel, status: o.paymentStatus,
        statusColor: o.paymentStatus === "Paid" ? "#17693F" : "#811844",
        items: o.items.map(i => ({
          name: i.name,
          qty: i.qty + (i.qty === 1 ? " pc" : " pcs"),
          options: [i.options].filter(Boolean).concat(i.note ? ["Note: " + i.note] : []),
          total: this.money(i.lineTotal)
        })),
        note: o.items.map(i => i.note).filter(Boolean).join(" · "),
        hasNote: o.items.some(i => !!i.note),
        subtotal: this.money(o.subtotal),
        discount: this.money(o.discount), hasDiscount: o.discount > 0,
        delivery: o.isPickup ? "Free" : this.money(o.deliveryFee)
      };

      this.persist({});
      this.setState({ orderBusy: false });
      this.nav("success", {
        cart:{}, couponOk:false, coupon:"",
        orderNo: o.orderNo, paid: this.money(o.total), paidDate: o.deliveryDate,
        invoice, exportNote:""
      });
    } catch (e) {
      this.setState({ orderBusy: false });
      this.flash("Could not reach the kitchen. Please try again.");
    }
  }
  validateAuth() {
    const s = this.state, f = s.form, err = {}, reg = s.authMode === "register";
    if (reg && f.name.trim().length < 2) err.name = "Please enter your full name.";
    if (s.phoneStep) {
      if (!/^1[3-9]\d{8}$/.test(f.phone.trim())) err.phone = "Enter 10 digits starting 13–19, e.g. 17XXXXXXXX.";
      this.setState({ err });
      return Object.keys(err).length === 0;
    }
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(f.email.trim())) err.email = "Enter a valid email address.";
    if (reg && !/^1[3-9]\d{8}$/.test(f.phone.trim())) err.phone = "Enter 10 digits starting 13–19, e.g. 17XXXXXXXX.";
    if (f.pw.length < 8) err.pw = "Password must be at least 8 characters.";
    if (reg && f.pw2 !== f.pw) err.pw2 = "Passwords do not match.";
    if (reg && !s.terms) err.terms = "Please accept the Terms & Conditions.";
    this.setState({ err });
    return Object.keys(err).length === 0;
  }
  orderVm(o, ix) {
    // Status comes from the database now, so tolerate a value with no palette
    // entry rather than throwing mid-render.
    const st = STATUS[o.status] || { bg:"#EADFE2", fg:"#561530" };
    const subtotal = o.items.reduce((a, i) => a + i[1] * i[2], 0);
    const delivery = o.status === "Cancelled" ? 0 : 80;
    return {
      no:o.no, date:o.date, payment:o.payment, status:o.status, statusBg:st.bg, statusFg:st.fg,
      itemsLabel: o.items.length + (o.items.length === 1 ? " item" : " items"),
      total: this.money(subtotal - o.discount + delivery),
      open: () => this.nav("orderdetail", { orderIx: ix })
    };
  }

  catalogueForModel() {
    return PRODUCTS.map(p => p.name + " | " + p.cat + " | " + p.price + " tk" + (p.note ? " | " + p.note : "") + " | " + p.kcal + " kcal/100g | " + p.ing).join("\n");
  }
  zonesForModel() {
    return ZONES.map(z => z.name + ": " + (z.fee === null ? "not served" : z.fee + " tk")).join("; ");
  }
  cartForModel() {
    const keys = Object.keys(this.state.cart);
    if (!keys.length) return "empty";
    return keys.map(k => {
      const l = this.state.cart[k];
      const p = PRODUCTS.find(x => x.id === l.id);
      const o = this.optionLabel(l);
      return l.qty + " x " + p.name + (o ? " (" + o + ")" : "");
    }).join("; ");
  }
  pushChat(role, text) {
    this.setState(s => ({ chatLog: s.chatLog.concat([{ role, text }]) }));
  }
  scrollChat() {
    const el = this.chatScroll;
    if (el) requestAnimationFrame(() => { el.scrollTop = el.scrollHeight; });
  }
  async askAgent(text) {
    const msg = (text || "").trim();
    if (!msg || this.state.chatBusy) return;
    this.pushChat("user", msg);
    this.setState({ chatDraft: "", chatBusy: true });
    this.scrollChat();

    const history = this.state.chatLog.concat([{ role: "user", text: msg }])
      .map(m => ({ role: m.role === "user" ? "user" : "assistant", content: m.text }));

    const system = [
      "You are the order assistant for Dacca Delights, a small-batch bakery cloud kitchen in Dhaka, Bangladesh.",
      "Voice: warm, brief, craft-focused. Two or three sentences at most unless listing items. Never use emoji or markdown formatting.",
      "Prices are in Bangladeshi taka, written like 350 tk. Never invent products, prices or delivery fees — use only the data below.",
      "",
      "MENU (name | category | price | unit or minimum | energy per 100g | key ingredients):",
      this.catalogueForModel(),
      "",
      "BAGEL MINIMUM: single bagels share one minimum of " + BAGEL_POOL_MOQ + " pieces, and the customer may mix any flavours to reach it. Jerusalem Bagel has its own minimum of 2. Bagel Bunches are pre-set and exempt. Never let an order go through with fewer than " + BAGEL_POOL_MOQ + " pooled bagels.",
      "CHICKEN PUFFS: Chicken Puff (200 tk, minimum 2) and Mini Chicken Puff (70 tk, minimum 20) are each one product with a mandatory filling choice — Spicy or Creamy. Always ask which before adding.",
      "BREAD RULES: most items in the Breads category need a sugar choice (Standard or Sugar-Free) and a format (Whole Loaf or Sliced). If Sliced, also a thickness (Regular Slice, Thick Slice or Thin Slice). Ask for these before adding such a bread to the cart.",
      "These breads are sold as-is and take NO sugar or slicing options — never ask about them: " + NO_BREAD_OPTIONS.join(", ") + ".",
      "DELIVERY: self-pickup from North Kafrul is free. Delivery fees by area — " + this.zonesForModel() + ".",
      "Cash on delivery is only available in Dhaka Cantonment, Gulshan, Banani, Baridhara Diplomatic Zone and Baridhara DOHS. Everywhere else pays online.",
      "HOURS: baking 6am to 2pm daily, orders taken until 8pm.",
      "DELIVERY DATE: orders before " + cutoffHour + ":00 can choose same-day; after that the earliest is tomorrow. Right now same-day is " + (sameDayOpen ? "available" : "closed") + ". Customers can book up to " + windowDays + " days ahead.",
      "",
      "CURRENT CART: " + this.cartForModel(),
      "",
      "Use add_to_cart when the customer has chosen something concrete. Use open_page to take them to the cart, checkout, menu or a product. Confirm what you did in plain words after using a tool."
    ].join("\n");

    const tools = [
      {
        name: "add_to_cart",
        description: "Add a menu item to the customer's cart. For Breads, sugar and form are required.",
        input_schema: {
          type: "object",
          properties: {
            product: { type: "string", description: "Exact product name from the menu" },
            qty: { type: "number", description: "Quantity, default 1" },
            sugar: { type: "string", enum: ["Standard", "Sugar-Free"] },
            form: { type: "string", enum: ["Whole Loaf", "Sliced"] },
            slice: { type: "string", enum: ["Regular Slice", "Thick Slice", "Thin Slice"] },
            filling: { type: "string", enum: ["Spicy", "Creamy"] },
            note: { type: "string", description: "Any special instruction from the customer" }
          },
          required: ["product"]
        },
        run: async input => {
          const want = String(input.product || "").toLowerCase();
          const p = PRODUCTS.find(x => x.name.toLowerCase() === want)
            || PRODUCTS.find(x => x.name.toLowerCase().includes(want));
          if (!p) return "No product matches that name. Ask the customer to pick from the menu.";
          if (needsFilling(p) && !input.filling) {
            return p.name + " needs a filling choice — Spicy or Creamy. Ask the customer, then call again.";
          }
          if (needsSugar(p) && (!input.sugar || (needsBreadOptions(p) && !input.form))) {
            return "That bread needs a sugar choice and a format first. Ask the customer, then call again.";
          }
          if (input.form === "Sliced" && !input.slice) {
            return "Sliced bread needs a thickness. Ask the customer, then call again.";
          }
          this.add(p.id, Math.max(1, Math.round(input.qty || 1)), {
            sugar: input.sugar || "", form: input.form || "", slice: input.slice || "", filling: input.filling || "", note: input.note || ""
          });
          return "Added " + (input.qty || 1) + " x " + p.name + ". Cart is now: " + this.cartForModel();
        }
      },
      {
        name: "open_page",
        description: "Navigate the customer to a page in the shop.",
        input_schema: {
          type: "object",
          properties: {
            page: { type: "string", enum: ["menu", "cart", "checkout", "about", "bulk"] },
            product: { type: "string", description: "Product name, to open its detail page instead" }
          }
        },
        run: async input => {
          if (input.product) {
            const want = String(input.product).toLowerCase();
            const p = PRODUCTS.find(x => x.name.toLowerCase() === want)
              || PRODUCTS.find(x => x.name.toLowerCase().includes(want));
            if (p) { this.openProduct(p); return "Opened " + p.name + "."; }
          }
          const page = input.page || "menu";
          if (page === "menu") this.nav("menu", { category:"Best Sellers", shown:8, query:"" });
          else this.nav(page);
          return "Opened the " + page + " page.";
        }
      }
    ];

    try {
      const reply = await window.claude.complete({ system, messages: history, tools, max_tokens: 700 });
      this.pushChat("assistant", (reply || "").trim() || "Sorry — I did not catch that. Could you say it another way?");
    } catch (err) {
      this.pushChat("assistant", "I could not reach the kitchen just then. Try again, or message us on WhatsApp at +880 1622 823269.");
    }
    this.setState({ chatBusy: false });
    this.scrollChat();
  }

  renderVals() {
    const s = this.state;
    const detailP = PRODUCTS.find(p => p.slug === s.slug) || PRODUCTS[0];
    const q = s.query.trim().toLowerCase();
    const searching = q.length > 0;
    const matches = PRODUCTS.filter(p => (p.name + " " + p.cat + " " + p.note).toLowerCase().includes(q));
    const pool = searching ? matches
      : (s.category === "Best Sellers"
          ? FEATURED.map(n => PRODUCTS.find(p => p.name === n)).filter(Boolean)
          : PRODUCTS.filter(p => p.cat === s.category));
    const listed = pool.slice(0, s.shown);
    const lines = Object.keys(s.cart).map(key => {
      const l = s.cart[key];
      const p = PRODUCTS.find(x => x.id === l.id);
      const opt = this.optionLabel(l);
      return {
        name: p.name, image: p.image, qty: l.qty,
        qtyName: l.qty + " × " + p.name,
        options: opt, hasOptions: !!opt,
        note: l.note, hasNote: !!l.note, optionLabel: opt, pid: p.id, key,
        unitLine: this.money(p.price) + (p.note ? " · " + p.note : ""),
        lineTotal: this.money(p.price * l.qty),
        inc: () => this.setQty(key, l.qty + 1),
        dec: () => this.setQty(key, l.qty - 1),
        remove: () => this.setQty(key, 0)
      };
    });
    const subtotal = Object.keys(s.cart).reduce((a, k) => a + PRODUCTS.find(p => p.id === s.cart[k].id).price * s.cart[k].qty, 0);
    const discount = s.couponOk ? subtotal * 0.2 : 0;
    const freeOver = this.props.freeDeliveryOver ?? 2000;
    const pickupZone = this.props.pickupZone ?? "North Kafrul";
    const cutoffHour = this.props.sameDayCutoffHour ?? 9;
    const windowDays = this.props.bookingWindowDays ?? 30;
    const now = new Date();
    const sameDayOpen = now.getHours() < cutoffHour;
    const DAYS = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    const DAYS_FULL = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    const MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const MON = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    const iso = d => d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") + "-" + String(d.getDate()).padStart(2, "0");
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const first = new Date(today.getFullYear(), today.getMonth(), today.getDate() + (sameDayOpen ? 0 : 1));
    const last = new Date(today.getFullYear(), today.getMonth(), today.getDate() + windowDays);
    const firstIso = iso(first), lastIso = iso(last);
    const chosenDate = s.deliveryDate && s.deliveryDate >= firstIso && s.deliveryDate <= lastIso ? s.deliveryDate : "";
    const prettyDate = v => {
      const p = v.split("-").map(Number);
      const d = new Date(p[0], p[1] - 1, p[2]);
      const label = DAYS_FULL[d.getDay()] + ", " + d.getDate() + " " + MON[d.getMonth()];
      if (v === iso(today)) return "Today — " + label;
      if (v === iso(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1))) return "Tomorrow — " + label;
      return label;
    };
    const buildMonth = offset => {
      const base = new Date(now.getFullYear(), now.getMonth() + offset, 1);
      const lead = base.getDay();
      const count = new Date(base.getFullYear(), base.getMonth() + 1, 0).getDate();
      const cells = [];
      for (let i = 0; i < lead; i++) cells.push({ day:"", disabled:true, blank:true });
      for (let d = 1; d <= count; d++) {
        const cur = new Date(base.getFullYear(), base.getMonth(), d);
        const v = iso(cur);
        const open = v >= firstIso && v <= lastIso;
        const on = open && v === chosenDate;
        cells.push({
          day: String(d), value: v, disabled: !open, on, open,
          aria: open ? prettyDate(v) : v + " unavailable",
          select: open ? (() => this.setState({ deliveryDate: v })) : (() => {})
        });
      }
      return {
        title: MONTHS[base.getMonth()] + " " + base.getFullYear(),
        dayNames: DAYS,
        cells: cells.map(c => ({
          day: c.day,
          aria: c.aria || "",
          disabled: !!c.disabled,
          weight: c.on ? "700" : "500",
          cursor: c.disabled ? "default" : "pointer",
          bg: c.on ? "#561530" : (c.blank ? "transparent" : (c.open ? "#FFF9F1" : "transparent")),
          fg: c.on ? "#FFF9F1" : (c.open ? "#2B171F" : "#D3C6CA"),
          border: c.on ? "#561530" : (c.open ? "#EADFE2" : "transparent"),
          select: c.select || (() => {})
        }))
      };
    };
    const zoneRow = ZONES.find(z => z.name === s.zone) || null;
    const zoneBlocked = false;
    const zoneLimited = !s.pickup && !!zoneRow && !!zoneRow.limited;
    const delivery = s.pickup ? 0 : (zoneRow && zoneRow.fee !== null ? zoneRow.fee : 0);
    const codAllowed = s.pickup || (!!zoneRow && COD_ZONES.indexOf(zoneRow.name) >= 0);
    const poolLines = lines.filter(l => inBagelPool(PRODUCTS.find(p => p.id === l.pid)));
    const poolQty = poolLines.reduce((a, l) => a + l.qty, 0);
    const poolShort = poolQty > 0 ? Math.max(0, BAGEL_POOL_MOQ - poolQty) : 0;
    const itemShort = lines
      .map(l => {
        const p = PRODUCTS.find(x => x.id === l.pid);
        const min = p ? ITEM_MOQ[p.name] : 0;
        return min && l.qty < min ? { name: p.name, need: min - l.qty, key: l.key } : null;
      })
      .filter(Boolean);
    const moqOk = poolShort === 0 && itemShort.length === 0;
    const nameOk = s.firstName.trim().length > 1 && s.lastName.trim().length > 1;
    const addressOk = s.pickup || (s.house.trim().length > 0 && s.line1.trim().length > 1);
    const localOk = /^1[3-9]\d{8}$/.test(s.localPhone);
    const waOk = s.waSame ? localOk : (s.waCode.trim().length > 1 && s.waNumber.replace(/\D/g, "").length >= 6);
    const showFormat = needsBreadOptions(detailP);
    const showSugar = needsSugar(detailP);
    const showFilling = needsFilling(detailP);
    const optsOk = (!showSugar || !!s.sugar) && (!showFilling || !!s.filling) && (!showFormat || (s.bform && (s.bform !== "Sliced" || s.slice)));
    const total = subtotal - discount + delivery;

    const navLinks = [
      { name:"Home", page:"home" }, { name:"Menu", page:"menu" }, { name:"The Team", page:"about" },
      { name:"About", page:"about" }, { name:"Contact", page:"about" }
    ].map(l => ({ name: l.name, go: () => this.nav(l.page, l.cat ? { category: l.cat } : {}) }));

    return {
      isHome: s.page === "home", isMenu: s.page === "menu", isProduct: s.page === "product", isCart: s.page === "cart",
      isCheckout: s.page === "checkout", isSuccess: s.page === "success", isAccount: s.page === "account",
      isAuth: s.page === "auth", isAbout: s.page === "about",
      navLinks, navRef: el => { this.navEl = el; },
      menuOpen: s.menuOpen, searchOpen: s.searchOpen, toast: s.toast,
      openMenu: () => this.setState({ menuOpen:true }), closeMenu: () => this.setState({ menuOpen:false }),
      toggleSearch: () => this.setState({ searchOpen: !s.searchOpen }),
      query: s.query, onQuery: e => this.setState({ query: e.target.value, shown: 8 }),
      runSearch: () => this.nav("menu", { query: s.query, shown: 8 }),
      goHome: () => this.nav("home"), goMenu: () => this.nav("menu", { category:"Best Sellers", shown:8, query:"" }),
      goCakes: () => this.nav("menu", { category:"Breads" }), goCart: () => this.nav("cart"),
      goCheckout: () => { if (!moqOk) { this.flash(poolShort > 0 ? "Bagels come in batches of " + BAGEL_POOL_MOQ : "One item is below its minimum"); return; } this.nav("checkout"); }, goAbout: () => this.nav("about"),
      goAccount: () => this.nav("account"), goOrders: () => this.nav("account", { accountTab:"Orders" }),
      cartCount: Object.values(s.cart).reduce((a, l) => a + l.qty, 0),
      hasCart: Object.keys(s.cart).length > 0,
      hasQuery: !!s.query,
      clearQuery: () => this.setState({ query: "", shown: 8 }),
      onSearchKey: e => { if (e.key === "Enter") { e.preventDefault(); this.nav("menu", { query: s.query, shown: 8 }); } },
      cartLines: lines, cartEmpty: lines.length === 0,
      moqOk,
      moqWarn: !moqOk,
      moqBtnBg: moqOk ? "#F5AD18" : "#EADFE2",
      moqTitle: poolShort > 0
        ? "Add " + poolShort + " more " + (poolShort === 1 ? "bagel" : "bagels")
        : (itemShort.length ? "Add " + itemShort[0].need + " more " + itemShort[0].name : ""),
      moqBody: poolShort > 0
        ? "Bagels are baked in batches of " + BAGEL_POOL_MOQ + ". Mix any flavours you like — you have " + poolQty + " so far."
        : (itemShort.length ? itemShort[0].name + " has a minimum of " + ITEM_MOQ[itemShort[0].name] + " pieces." : ""),
      moqFix: () => {
        if (poolShort > 0) {
          const target = poolLines.slice().sort((a, b) => b.qty - a.qty)[0];
          if (target) { this.setQty(target.key, target.qty + poolShort); this.flash("Topped up to " + BAGEL_POOL_MOQ + " bagels"); }
        } else if (itemShort.length) {
          const it = itemShort[0];
          this.setQty(it.key, this.lineQty(it.key) + it.need);
          this.flash(it.name + " topped up to its minimum");
        }
      },
      subtotal: this.money(subtotal), discount: this.money(discount),
      deliveryLabel: s.pickup ? "Free — pickup" : (zoneRow ? this.money(zoneRow.fee) : "Select an area"),

      deliveryDate: chosenDate,
      chosenDateLabel: chosenDate ? (chosenDate ? prettyDate(chosenDate) : "—") : "No date selected",
      dateOk: !!chosenDate,
      chosenDateColor: chosenDate ? "#561530" : "#9E1C60",
      calendars: [buildMonth(0), buildMonth(1)],
      dateLabel: s.pickup ? "Pickup date" : "Delivery date",
      cutoffNote: (sameDayOpen
        ? "Same-day " + (s.pickup ? "pickup" : "delivery") + " until " + cutoffHour + ":00 today. "
        : "Past the " + cutoffHour + ":00 cutoff — earliest is tomorrow. ") + "Booking up to " + windowDays + " days ahead.",
      cutoffColor: sameDayOpen ? "#17693F" : "#811844",
      firstName: s.firstName, lastName: s.lastName,
      house: s.house, onHouse: e => this.setState({ house: e.target.value }),
      line1: s.line1, onLine1: e => this.setState({ line1: e.target.value }),
      line2: s.line2, onLine2: e => this.setState({ line2: e.target.value }),
      zip: s.zip, onZip: e => this.setState({ zip: e.target.value.replace(/\D/g, "").slice(0, 4) }),
      addressError: (!s.pickup && (s.house || s.line1) && !addressOk) ? "House name or number and address line 1 are both required." : "",
      onFirstName: e => this.setState({ firstName: e.target.value }),
      onLastName: e => this.setState({ lastName: e.target.value }),
      nameError: (s.firstName || s.lastName) && !nameOk ? "Please enter both your first and last name." : "",
      pickup: s.pickup, pickupZone,
      pickupMark: s.pickup ? "✓" : "",
      pickupBg: s.pickup ? "#F5AD18" : "#FFFFFF",
      pickupBorder: s.pickup ? "#F5AD18" : "#EADFE2",
      notPickup: !s.pickup,
      togglePickup: () => this.setState(st => st.pickup
        ? { pickup: false }
        // switching to pickup clears every delivery-only field so nothing stale
        // is submitted or printed on the invoice
        : { pickup: true, zone: "", house: "", line1: "", line2: "", zip: "", mapsUrl: "", geoStatus: "" }),
      zone: s.zone,
      zoneSuffix: s.pickup ? " — not needed for pickup" : "",
      zoneBlocked,
      zoneOptions: [{ value:"", label:"Select your area" }].concat(ZONES.map(z => ({ value:z.name, label:z.name }))),
      zoneLimited: !s.pickup && !!zoneRow && !!zoneRow.limited,
      zoneFeeLabel: s.pickup ? "Free — self-pickup"
        : (zoneRow ? (zoneRow.limited ? "From " + this.money(zoneRow.fee) : this.money(zoneRow.fee)) : "Select your area"),
      onZone: e => {
        const v = e.target.value;
        const row = ZONES.find(z => z.name === v);
        const cod = !!row && COD_ZONES.indexOf(row.name) >= 0;
        this.setState({ zone: v, payment: (s.payment === "cod" && !cod) ? "" : s.payment });
      },
      localPhone: s.localPhone,
      onLocalPhone: e => this.setState({ localPhone: e.target.value.replace(/\D/g, "").slice(0, 10) }),
      localPhoneError: s.localPhone && !localOk ? "Enter 10 digits starting 13–19, e.g. 17XXXXXXXX." : "",
      waSame: s.waSame,
      waSameMark: s.waSame ? "✓" : "",
      waSameBg: s.waSame ? "#F5AD18" : "#FFFFFF",
      waSameBorder: s.waSame ? "#F5AD18" : "#EADFE2",
      toggleWaSame: () => this.setState({ waSame: !s.waSame, waCode: "+880", waNumber: s.waSame ? s.localPhone : "" }),
      waCode: s.waSame ? "+880" : s.waCode,
      onWaCode: e => this.setState({ waCode: e.target.value }),
      waNumber: s.waSame ? s.localPhone : s.waNumber,
      onWaNumber: e => this.setState({ waNumber: e.target.value.replace(/[^0-9\s-]/g, "") }),
      countryCodes: COUNTRY_CODES,
      mapsUrl: s.mapsUrl,
      onMapsUrl: e => this.setState({ mapsUrl: e.target.value }),
      geoStatus: s.geoStatus,
      captureLocation: () => {
        if (typeof navigator === "undefined" || !navigator.geolocation) { this.setState({ geoStatus:"Geolocation not available here" }); return; }
        this.setState({ geoStatus: "Locating…" });
        navigator.geolocation.getCurrentPosition(
          pos => this.setState({
            mapsUrl: "https://maps.google.com/?q=" + pos.coords.latitude.toFixed(6) + "," + pos.coords.longitude.toFixed(6),
            geoStatus: "Location captured"
          }),
          () => this.setState({ geoStatus:"Could not get your location — paste a maps link instead" }),
          { timeout: 10000 }
        );
      },
      codHidden: !codAllowed,
      codHiddenNote: "Cash payment is available for self-pickup, and for delivery to Dhaka Cantonment, Gulshan, Banani, Baridhara Diplomatic Zone and Baridhara DOHS. Elsewhere, pay online.",
      orderBlocked: !(Object.keys(s.cart).length && moqOk && nameOk && addressOk && localOk && waOk && !!chosenDate && (s.pickup || (zoneRow && !zoneBlocked)) && !!s.payment),
      orderBlockedReason: Object.keys(s.cart).length === 0 ? "Your cart is empty."
        : !moqOk ? (poolShort > 0 ? "Add " + poolShort + " more bagels to meet the minimum of " + BAGEL_POOL_MOQ + "." : itemShort[0].name + " is below its minimum order.")
        : !chosenDate ? "Choose a " + (s.pickup ? "pickup" : "delivery") + " date."
        : !nameOk ? "Enter your first and last name."
        : !addressOk ? "Enter your house name or number and address line 1."
        : (!s.pickup && !zoneRow) ? "Choose a delivery area, or select self-pickup."
        : zoneBlocked ? "We do not deliver to that area — choose self-pickup or another area."
        : !localOk ? "Enter a valid local mobile number for the rider."
        : !waOk ? "Enter a valid WhatsApp number for confirmation."
        : !s.payment ? "Choose a payment method."
        : "",
      placeBg: !(Object.keys(s.cart).length && moqOk && nameOk && addressOk && localOk && waOk && !!chosenDate && (s.pickup || (zoneRow && !zoneBlocked)) && !!s.payment) ? "#EADFE2" : "#F5AD18",
      placeCursor: !(Object.keys(s.cart).length && moqOk && nameOk && addressOk && localOk && waOk && !!chosenDate && (s.pickup || (zoneRow && !zoneBlocked)) && !!s.payment) ? "not-allowed" : "pointer",

      hasOpts: showSugar || showFilling,
      showSugar,
      showFilling,
      fillingOpts: (FILLINGS[detailP.name] || []).map(n => ({ name:n, select: () => this.setState({ filling:n }),
        bg: s.filling === n ? "#561530" : "#FFFFFF", fg: s.filling === n ? "#FFF9F1" : "#561530",
        border: s.filling === n ? "#561530" : "#EADFE2",
        dotBg: s.filling === n ? "#F5AD18" : "transparent", dotBorder: s.filling === n ? "#F5AD18" : "#C9B7BD" })),
      showFormat,
      showSlice: showFormat && s.bform === "Sliced",
      sugarOpts: ["Standard","Sugar-Free"].map(n => ({ name:n, select: () => this.setState({ sugar:n }),
        bg: s.sugar === n ? "#561530" : "#FFFFFF", fg: s.sugar === n ? "#FFF9F1" : "#561530",
        border: s.sugar === n ? "#561530" : "#EADFE2",
        dotBg: s.sugar === n ? "#F5AD18" : "transparent", dotBorder: s.sugar === n ? "#F5AD18" : "#C9B7BD" })),
      formOpts: ["Whole Loaf","Sliced"].map(n => ({ name:n, select: () => this.setState({ bform:n, slice: n === "Sliced" ? s.slice : "" }),
        bg: s.bform === n ? "#561530" : "#FFFFFF", fg: s.bform === n ? "#FFF9F1" : "#561530",
        border: s.bform === n ? "#561530" : "#EADFE2",
        dotBg: s.bform === n ? "#F5AD18" : "transparent", dotBorder: s.bform === n ? "#F5AD18" : "#C9B7BD" })),
      sliceOpts: ["Regular Slice","Thick Slice","Thin Slice"].map(n => ({ name:n, select: () => this.setState({ slice:n }),
        bg: s.slice === n ? "#561530" : "#FFFFFF", fg: s.slice === n ? "#FFF9F1" : "#561530",
        border: s.slice === n ? "#561530" : "#EADFE2",
        dotBg: s.slice === n ? "#F5AD18" : "transparent", dotBorder: s.slice === n ? "#F5AD18" : "#C9B7BD" })),
      notes: s.notes,
      onNotes: e => this.setState({ notes: e.target.value }),
      addBlocked: !optsOk,
      addBg: optsOk ? "#561530" : "#EADFE2",
      addFg: optsOk ? "#FFF9F1" : "#75666B",
      buyBg: optsOk ? "#F5AD18" : "#EADFE2",
      addCursor: optsOk ? "pointer" : "not-allowed",
      addBlockedReason: (showFilling && !s.filling) ? "Choose Spicy or Creamy to continue."
        : (showSugar && !s.sugar) ? "Choose Standard or Sugar-Free to continue."
        : !s.bform ? "Choose Whole Loaf or Sliced to continue."
        : (s.bform === "Sliced" && !s.slice) ? "Choose a slice thickness to continue."
        : "",
      total: this.money(total),
      coupon: s.coupon, couponApplied: s.couponOk, couponMessage: "SWEET20 applied — 20% off your order",
      onCoupon: e => this.setState({ coupon: e.target.value }),
      applyCoupon: () => {
        const ok = s.coupon.trim().toUpperCase() === "SWEET20";
        this.setState({ couponOk: ok });
        this.flash(ok ? "Coupon applied — 20% off" : "That code isn't valid");
      },
      claimOffer: () => { this.setState({ coupon:"SWEET20", couponOk:true }); this.nav("menu", { category:"Best Sellers", shown:8 }); this.flash("SWEET20 saved to your cart"); },
      signatureImage: SHOT("Bread.jpeg"),
      aboutImage: IMG("1556910103-1c02745aae4d", 800),
      chatOpen: s.chatOpen, chatClosed: !s.chatOpen,
      openChat: () => { this.setState({ chatOpen: true }); this.scrollChat(); },
      closeChat: () => this.setState({ chatOpen: false }),
      chatOffset: Object.keys(s.cart).length ? "82px" : "20px",
      chatScrollRef: el => { this.chatScroll = el; },
      chatEmpty: s.chatLog.length === 0,
      chatBusy: s.chatBusy,
      chatSendBg: s.chatBusy ? "#EADFE2" : "#F5AD18",
      chatSendCursor: s.chatBusy ? "not-allowed" : "pointer",
      chatDraft: s.chatDraft,
      onChatDraft: e => this.setState({ chatDraft: e.target.value }),
      onChatKey: e => { if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); this.askAgent(s.chatDraft); } },
      sendChat: () => this.askAgent(s.chatDraft),
      chatPrompts: [
        "What is good today?",
        "Do you deliver to Mohammadpur?",
        "Add 4 butter croissants",
        "I need bread for a party of 20"
      ].map(t => ({ text: t, send: () => this.askAgent(t) })),
      chatLog: s.chatLog.map(m => ({
        text: m.text,
        justify: m.role === "user" ? "flex-end" : "flex-start",
        bg: m.role === "user" ? "#561530" : "#FFFFFF",
        fg: m.role === "user" ? "#FFF9F1" : "#2B171F",
        border: m.role === "user" ? "#561530" : "#EADFE2",
        radius: m.role === "user" ? "16px 16px 4px 16px" : "16px 16px 16px 4px"
      })),
      onImgError: e => {
        const el = e.currentTarget || e.target;
        if (el && !el.dataset.fb) {
          el.dataset.fb = "1";
          el.removeAttribute("srcset");
          el.removeAttribute("sizes");
          el.src = FALLBACK(encodeURIComponent(el.alt || "bakery"));
        }
      },
      categoryChips: CATS.map(c => ({
        name: c,
        bg: s.category === c ? "#561530" : "#FFFFFF",
        fg: s.category === c ? "#FFF9F1" : "#561530",
        border: s.category === c ? "#561530" : "#EADFE2",
        select: () => this.nav("menu", { category: c, shown: 8, query: "" })
      })),
      featured: FEATURED.map(n => PRODUCTS.find(p => p.name === n)).filter(Boolean).map(p => this.card(p)),
      listed: listed.map(p => this.card(p)),
      hasMore: pool.length > listed.length,
      viewMore: () => this.setState({ shown: s.shown + 8 }),
      hasQuery: q.length > 0,
      clearQuery: () => this.setState({ query:"", shown:8 }),
      showSuggest: q.length > 1 && matches.length > 0,
      suggestions: matches.slice(0, 5).map(p => ({
        name:p.name, category:p.cat, image:p.image, price:this.money(p.price),
        open: () => this.openProduct(p)
      })),
      browseBest: () => this.setState({ query:"", category:"Best Sellers", shown:8 }),
      showMobileCart: Object.keys(s.cart).length > 0,
      noResults: listed.length === 0,
      menuKicker: s.category.toUpperCase(),
      menuTitle: q.length > 0 ? "Search results" : s.category,
      resultLabel: pool.length + (pool.length === 1 ? " product found" : " products found"),
      detail: (() => {
        const support = SUPPORT[detailP.cat] || SUPPORT["Breads"];
        const shots = [{ src: detailP.image, id: null }]
          .concat(support.map(id => ({ src: IMG(id, 1200), id })))
          .filter((sh, i, all) => all.findIndex(o => o.src === sh.src) === i)
          .slice(0, 3);
        const ix = Math.min(s.shot, shots.length - 1);
        const active = shots[ix];
        return {
          name: detailP.name, categoryUpper: detailP.cat.toUpperCase(),
          image: active.src,
          imageSet: active.id ? IMG_SET(active.id) : "",
          imageSizes: active.id ? "(min-width:900px) 50vw, 100vw" : null,
          note: detailP.note || CAT_META[detailP.cat].blurb,
          desc: CAT_META[detailP.cat].blurb + " Every order is baked the morning it goes out, so nothing sits overnight.",
          price: this.money(detailP.price), unit: detailP.note,
          ing: detailP.ing, kcal: detailP.kcal + " kcal / 100g",
          thumbs: shots.map((sh, i) => ({
            src: sh.src,
            set: sh.id ? IMG_SET(sh.id) : "",
            sizes: sh.id ? "(min-width:900px) 160px, 30vw" : null,
            alt: detailP.name + " view " + (i + 1),
            border: i === ix ? "#F5AD18" : "#EADFE2",
            select: () => this.setState({ shot: i })
          })),
          moqNote: inBagelPool(detailP)
            ? "Bagels come in batches of " + BAGEL_POOL_MOQ + " — mix any flavours to reach the minimum."
            : (ITEM_MOQ[detailP.name] ? "Minimum order " + ITEM_MOQ[detailP.name] + " pieces." : ""),
          hasMoqNote: inBagelPool(detailP) || !!ITEM_MOQ[detailP.name],
          points: ["Baked the morning of delivery — never frozen, never held over.",
                   "Self-pickup free from " + pickupZone + ", or Dhaka delivery from " + this.money(60) + ".",
                   "Custom sizes and event orders: message the kitchen."]
        };
      })(),
      qty: s.qty,
      incQty: () => this.setState({ qty: s.qty + 1 }),
      decQty: () => this.setState({ qty: Math.max(this.minQtyFor(detailP), s.qty - 1) }),
      addDetail: () => { if (!optsOk) { this.flash("Choose the required options first"); return; } this.add(detailP.id, s.qty, { sugar:s.sugar, form:s.bform, slice:s.slice, filling:s.filling, note:s.notes }); },
      buyDetail: () => { if (!optsOk) { this.flash("Choose the required options first"); return; } this.add(detailP.id, s.qty, { sugar:s.sugar, form:s.bform, slice:s.slice, filling:s.filling, note:s.notes }); this.nav("checkout"); },
      openSignature: () => { const sp = PRODUCTS.find(p => p.slug === "sourdough-bread"); if (sp) this.openProduct(sp); },
      related: PRODUCTS.filter(p => p.cat === detailP.cat && p.slug !== detailP.slug).slice(0, 4).map(p => this.card(p)),
      payments: [
        { key:"cod", name: s.pickup ? "Cash on Pickup" : "Cash on Delivery", note: s.pickup ? "Pay at the kitchen" : "Pay the rider", show: codAllowed },
        { key:"bkash", name:"bKash", note:"Gateway coming soon", show: true },
        { key:"card", name:"Card", note:"Gateway coming soon", show: true }
      ].filter(pm => pm.show).map(pm => ({
        name: pm.name, note: pm.note,
        bg: s.payment === pm.key ? "#FFF9F1" : "#FFFFFF",
        border: s.payment === pm.key ? "#9E1C60" : "#EADFE2",
        select: () => this.setState({ payment: pm.key })
      })),
      placeOrder: () => {
        if (Object.keys(s.cart).length === 0) { this.flash("Your cart is empty"); return; }
        if (!s.pickup && (!zoneRow || zoneBlocked)) { this.flash("Choose a deliverable area, or self-pickup"); return; }
        if (!chosenDate) { this.flash("Choose a delivery date"); return; }
        if (!nameOk) { this.flash("Enter your first and last name"); return; }
        if (!addressOk) { this.flash("Complete the delivery address"); return; }
        if (!localOk || !waOk) { this.flash("Check your phone numbers"); return; }
        if (!s.payment) { this.flash("Choose a payment method"); return; }
        this.submitOrder();
      },
      orderNo: s.orderNo, paidTotal: s.paid, paidDate: s.paidDate || "Tomorrow",
      invoice: s.invoice || {},
      invoiceRef: el => { this.invoiceEl = el; },
      exportBusy: s.exportBusy, exportNote: s.exportNote,
      savePng: () => this.exportInvoice("png"),
      saveJpeg: () => this.exportInvoice("jpeg"),
      savePdf: () => {
        if (typeof window === "undefined") return;
        document.body.classList.add("printinv");
        const done = () => { document.body.classList.remove("printinv"); window.removeEventListener("afterprint", done); };
        window.addEventListener("afterprint", done);
        window.print();
        setTimeout(done, 3000);
      },
      shareInvoice: () => this.shareInvoice(),
      isRegister: s.authMode === "register", isLogin: s.authMode === "login",
      idIsEmail: s.idMode === "email", idIsPhone: s.idMode === "phone",
      authModeWord: s.authMode === "register" ? "SIGN UP" : "SIGN IN",
      continueGoogle: () => {
        this.flash("Google sign-in — connect OAuth to finish this route");
        this.nav("account", { accountTab:"Dashboard", err:{} });
      },
      continueWhatsapp: () => this.setState({ idMode:"phone", phoneStep:true, err:{} }),
      phoneStep: s.phoneStep,
      closePhoneStep: () => this.setState({ phoneStep:false, idMode:"email", err:{} }),
      sendPhoneOtp: () => {
        if (!/^1[3-9]\d{8}$/.test(s.form.phone.trim())) { this.setState({ err:{ phone:"Enter 10 digits starting 13–19, e.g. 17XXXXXXXX." } }); return; }
        this.setState({ err:{} });
        this.sendOtp();
      },
      formStep: !s.otpStep, otpStep: s.otpStep,
      otpTarget: this.otpTargetLabel(),
      otpTargetKind: (s.otpVia || "email") === "phone" ? "number" : "email",
      otpDemo: s.otpCode,
      otpError: s.otpError,
      otpBoxes: s.otpDigits.map((d, i) => ({
        n: i + 1, value: d,
        border: d ? "#F5AD18" : "#EADFE2",
        ref: el => { this.otpRefs = this.otpRefs || []; this.otpRefs[i] = el; },
        onChange: e => this.setOtpDigit(i, e.target.value),
        onKey: e => {
          if (e.key === "Backspace" && !s.otpDigits[i] && this.otpRefs[i - 1]) this.otpRefs[i - 1].focus();
          if (e.key === "Enter") this.setState({ otpEnter: Date.now() });
        }
      })),
      otpIncomplete: s.otpDigits.join("").length < 6,
      otpBtnBg: s.otpDigits.join("").length < 6 ? "#EADFE2" : "#F5AD18",
      otpCursor: s.otpDigits.join("").length < 6 ? "not-allowed" : "pointer",
      verifyOtp: () => {
        if (s.otpDigits.join("") !== s.otpCode) { this.setState({ otpError:"That code does not match. Check and try again." }); return; }
        clearInterval(this._otpTimer);
        this.flash(s.authMode === "register" ? "Verified — welcome to Dacca Delights" : "Signed in");
        this.nav("account", { accountTab:"Dashboard", err:{}, otpStep:false, phoneStep:false, otpDigits:["","","","","",""] });
      },
      resendWait: s.resendIn > 0,
      resendColor: s.resendIn > 0 ? "#A79A9E" : "#811844",
      resendLabel: s.resendIn > 0 ? "Resend code in " + s.resendIn + "s" : "Resend code",
      resendOtp: () => { if (s.resendIn === 0) this.sendOtp(); },
      cancelOtp: () => { clearInterval(this._otpTimer); this.setState({ otpStep:false, otpError:"" }); },
      authTitle: s.otpStep ? "Verify Your Account" : (s.authMode === "register" ? "Create Account" : "Welcome Back"),
      authBlurb: s.otpStep
        ? "We sent you a one-time code to confirm it's really you."
        : (s.authMode === "register" ? "Sign up with your email or mobile number — we'll send a one-time code to verify it." : "Sign up to unlock fast pre-orders, order tracking, and delivery preferences."),
      authCta: s.authMode === "register" ? "Create Account" : "Login",
      authSwitchLead: s.authMode === "register" ? "Already have an account?" : "Don't have an account?",
      authSwitchCta: s.authMode === "register" ? "Login" : "Create one",
      toggleAuth: () => this.setState({ authMode: s.authMode === "register" ? "login" : "register", err:{}, otpStep:false, otpError:"", phoneStep:false, idMode:"email" }),
      form: {
        name:s.form.name, email:s.form.email, phone:s.form.phone, pw:s.form.pw, pw2:s.form.pw2,
        setname: e => this.setField("name", e.target.value),
        setemail: e => this.setField("email", e.target.value),
        setphone: e => this.setField("phone", e.target.value),
        setpw: e => this.setField("pw", e.target.value),
        setpw2: e => this.setField("pw2", e.target.value)
      },
      err: { name:s.err.name || "", email:s.err.email || "", phone:s.err.phone || "", pw:s.err.pw || "", pw2:s.err.pw2 || "", terms:s.err.terms || "" },
      pwType: s.showPw ? "text" : "password",
      pwToggleLabel: s.showPw ? "Hide" : "Show",
      togglePw: () => this.setState({ showPw: !s.showPw }),
      pwStrengthPct: this.strength(s.form.pw).pct,
      pwStrengthLabel: this.strength(s.form.pw).label,
      pwStrengthColor: this.strength(s.form.pw).color,
      toggleTerms: () => this.setState({ terms: !s.terms }),
      termsMark: s.terms ? "✓" : "", termsBg: s.terms ? "#F5AD18" : "#FFFFFF", termsBorder: s.terms ? "#F5AD18" : "#EADFE2",
      toggleRemember: () => this.setState({ remember: !s.remember }),
      rememberMark: s.remember ? "✓" : "", rememberBg: s.remember ? "#F5AD18" : "#FFFFFF", rememberBorder: s.remember ? "#F5AD18" : "#EADFE2",
      forgotPw: () => this.flash("Password reset link sent to your email"),
      submitAuth: () => {
        if (!this.validateAuth()) { this.flash("Please fix the highlighted fields"); return; }
        this.doAuth();
      },
      authBusy: s.authBusy,
      // Identity of the signed-in customer, from the PHP session.
      acctName: s.customerName || "Guest",
      acctFullName: [s.customerName, s.customerLastName].filter(Boolean).join(" ") || s.customerName || "Guest",
      acctEmail: s.customerEmail || "",
      acctPhone: s.customerPhone || "",
      acctInitials: ([s.customerName, s.customerLastName].filter(Boolean).join(" ") || "Guest")
        .trim().split(/\s+/).map(w => w[0]).join("").slice(0, 2).toUpperCase(),
      // Real logout: the server destroys the session, so this must leave the SPA.
      logout: () => {
        clearInterval(this._otpTimer);
        try { sessionStorage.removeItem("dd_draft"); } catch (e) {}
        window.location.href = DD_BASE + "auth/logout";
      },
      accountNav: ["Dashboard","Orders","Profile","Addresses","Password"].map(t => ({
        name: t === "Password" ? "Change Password" : t,
        icon: {"Dashboard":"◧","Orders":"▤","Profile":"◍","Addresses":"⌖","Password":"⚿"}[t],
        bg: s.accountTab === t ? "#F5AD18" : "transparent",
        fg: s.accountTab === t ? "#561530" : "rgba(255,249,241,0.82)",
        select: () => this.nav("account", { accountTab: t })
      })),
      tabDashboard: s.page === "account" && s.accountTab === "Dashboard",
      tabOrders: s.page === "account" && s.accountTab === "Orders",
      tabProfile: s.page === "account" && s.accountTab === "Profile",
      tabAddresses: s.page === "account" && s.accountTab === "Addresses",
      tabPassword: s.page === "account" && s.accountTab === "Password",
      showOrderList: s.page === "account" && (s.accountTab === "Dashboard" || s.accountTab === "Orders"),
      selectOrdersTab: () => this.nav("account", { accountTab:"Orders" }),
      orderCountLabel: ORDERS.length + " orders in total",
      orderColumns: ["Order ID","Date","Items","Total","Payment","Status","Action"],
      orderStats: [
        { label:"Total Orders", value:ORDERS.length, color:"#561530" },
        { label:"Pending", value:ORDERS.filter(o => o.status === "Pending" || o.status === "Confirmed").length, color:"#F5AD18" },
        { label:"Completed", value:ORDERS.filter(o => o.status === "Delivered").length, color:"#17693F" },
        { label:"Cancelled", value:ORDERS.filter(o => o.status === "Cancelled").length, color:"#B3261E" }
      ],
      orders: ORDERS.map((o, i) => this.orderVm(o, i)),
      isOrderDetail: s.page === "orderdetail",
      od: (() => {
        const o = ORDERS[s.orderIx] || ORDERS[0];
        // A signed-out visitor (or one with no orders yet) has an empty ORDERS
        // list. This runs on every render, so it must not assume an order.
        if (!o) {
          return { no:"", date:"", payment:"", status:"", statusBg:"#EADFE2", statusFg:"#561530",
                   customer:"", phone:"", address:"", notes:"", items:[],
                   subtotal:"", discount:"", hasDiscount:false, delivery:"", total:"",
                   cancellable:false, timeline:[] };
        }
        const st = STATUS[o.status] || { bg:"#EADFE2", fg:"#561530" };
        const sub = o.items.reduce((a, i) => a + i[1] * i[2], 0);
        const del = o.status === "Cancelled" ? 0 : 80;
        const stageIx = o.status === "Cancelled" ? -1 : Math.max(0, STAGES.indexOf(o.status === "Pending" ? "Order Placed" : o.status));
        return {
          no:o.no, date:o.date, payment:o.payment, status:o.status, statusBg:st.bg, statusFg:st.fg,
          // Details as recorded on this order, falling back to the profile
          // only for older rows that predate the snapshot columns.
          customer: o.customer || [s.customerName, s.customerLastName].filter(Boolean).join(" "),
          phone: o.phone || s.customerPhone || "",
          whatsapp: o.whatsapp || "", email: o.email || "",
          mapUrl: o.mapUrl || "", hasMap: !!o.mapUrl,
          address:o.address, notes:o.notes,
          items: o.items.map(i => {
            const p = PRODUCTS.find(x => x.name === i[0]);
            return { name:i[0], qty:i[1], unit:this.money(i[2]), total:this.money(i[1] * i[2]),
                     image: p ? p.image : CAT_META["Breads"].image };
          }),
          subtotal:this.money(sub), discount:this.money(o.discount), hasDiscount:o.discount > 0, delivery:this.money(del),
          total:this.money(sub - o.discount + del),
          cancellable: o.status === "Pending" || o.status === "Confirmed",
          timeline: STAGES.map((name, i) => ({
            name,
            bar: stageIx >= i ? "#F5AD18" : "#EADFE2",
            color: stageIx >= i ? "#561530" : "#A79A9E",
            weight: stageIx === i ? "700" : "500"
          }))
        };
      })(),
      reorder: () => { this.flash("Items added back to your cart"); this.nav("cart"); },
      downloadInvoice: () => this.flash("Invoice PDF generated"),
      cancelOrder: () => this.flash("Cancellation request sent to the kitchen"),
      addresses: [
        { label:"Home", line:"House 42, Road 7, Dhanmondi, Dhaka 1205", isDefault:true, border:"#F5AD18" },
        { label:"Office", line:"Level 8, Bay's Galleria, Gulshan 1, Dhaka 1212", isDefault:false, border:"#EADFE2" }
      ].map(a => Object.assign(a, { edit: () => this.flash("Editing " + a.label + " address"), remove: () => this.flash(a.label + " address removed") })),
      addAddress: () => this.flash("New address form opened"),
      uploadPhoto: () => this.flash("Choose a JPG, PNG or WEBP under 2MB"),
      saveProfile: () => this.flash("Profile updated"),
      savePassword: () => this.flash("Password updated"),
      isBulk: s.page === "bulk", bulkSent: s.bulkSent, bulkForm: !s.bulkSent,
      goBulk: () => this.nav("bulk", { bulkSent:false }),
      submitBulk: () => { this.nav("bulk", { bulkSent:true }); this.flash("Inquiry submitted"); },
      bulkHighlights: ["Custom Quantity","Special Pricing","Freshly Prepared","Delivery Available"],
      whyUs: [
        { icon:"◐", title:"Fresh Every Day", body:"Baked to order each morning. Nothing frozen, nothing held over from yesterday." },
        { icon:"◇", title:"Premium Ingredients", body:"French butter, Belgian chocolate, and flour milled for baking, not bulk." },
        { icon:"♡", title:"Made With Love", body:"A team of four. Everything shaped, filled and finished by hand." },
        { icon:"→", title:"Fast Delivery", body:"Dhaka-wide by 10am, or same-day within 3km of Dhanmondi." }
      ],
      stats: [ { value:"10+", label:"Years baking" }, { value:"50+", label:"Products" }, { value:"1,000+", label:"Happy customers" } ],
      testimonials: TESTIMONIALS.map(t => ({ name:t.name, stars:t.stars, quote:t.quote, item:t.item, initial:t.name.charAt(0) })),
      logoFallback: (e) => {
        const img = e.currentTarget;
        img.style.display = "none";
        const txt = img.nextElementSibling;
        if (txt) txt.style.display = "block";
      },
      gallery: GALLERY,
      contactCards: [
        { label:"KITCHEN", value:"House 42, Road 7, Dhanmondi, Dhaka 1205" },
        { label:"CONTACT", value:"+880 1622 823269 info@daccadelights.com" },
        { label:"WORKING HOURS", value:"Baking Hour: 9 am – 3 pm\nDelivery Hour: 5 pm - 9 pm" },
        { label:"SOCIAL", value:"@daccadelights on Instagram, Facebook and WhatsApp" }
      ],
      subscribe: () => this.flash("Thanks — you're on the list")
    };
  }
}
</script>
