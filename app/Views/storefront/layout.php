<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="<?= base_url("assets/storefront/support.js") ?>"></script>
</head>
<body>
<x-dc>
<helmet>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,600&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<style>
  body { margin:0; background:#FFF9F1; color:#2B171F; -webkit-font-smoothing:antialiased; }
  * { box-sizing:border-box; }
  a { color:#811844; text-decoration:none; }
  a:hover { color:#9E1C60; }
  input, button, select, textarea { font-family:'Plus Jakarta Sans', system-ui, sans-serif; font-weight:600; }
  input, select, textarea { font-weight:400; }
  h1, h2 { font-variation-settings:'opsz' 120, 'SOFT' 0, 'WONK' 0; }
  h3, h4 { font-variation-settings:'opsz' 36; }
  input:focus-visible, textarea:focus-visible, select:focus-visible, button:focus-visible { outline:2px solid #9E1C60; outline-offset:2px; }
  ::placeholder { color:#A79A9E; }
  @media print {
    body { background:#FFFFFF; }
    .noprint { display:none !important; }
    body.printinv .invhide { display:none !important; }
    body.printinv .invsection { display:block !important; padding:0 !important; margin:0 !important; max-width:none !important; text-align:left !important; gap:0 !important; }
    body.printinv .invoice { border:0 !important; border-radius:0 !important; box-shadow:none !important; padding:0 !important; max-width:none !important; margin:0 !important; }
    .invoice { border:0 !important; box-shadow:none !important; margin:0 !important; max-width:none !important; }
    @page { size:80mm auto; margin:4mm; }
  }
  .nb::-webkit-scrollbar { height:0; width:0; }
  .dnav { display:none; }
  .mcart { position:fixed; left:12px; right:12px; bottom:12px; z-index:70; }
  @media (min-width:900px) { .mcart { display:none; } .msearch { max-width:520px; } }
  @media (min-width:900px) { .dnav { display:flex; } .mnav { display:none; } }
  @media (min-width:1024px) { .hsearch { display:flex !important; } .msearchbtn { display:none !important; } }
  .acct { display:grid; grid-template-columns:1fr; gap:20px; align-items:start; }
  .asbnav { display:flex; flex-direction:row; overflow-x:auto; gap:6px; }
  .otable { display:none; overflow-x:auto; }
  .authpic { display:none; }
  @media (min-width:1150px) { .otable { display:block; } .ocards { display:none; } }
  @media (min-width:900px) {
    .acct { grid-template-columns:264px minmax(0,1fr); }
    .asbnav { flex-direction:column; overflow:visible; }

    .authpic { display:block; }
  }
  @keyframes toastIn { from { opacity:0; transform:translateY(14px) } to { opacity:1; transform:translateY(0) } }
</style>
</helmet>

<div style="font-family:'Plus Jakarta Sans', system-ui, sans-serif; background:#FFF9F1; min-height:100vh; display:flex; flex-direction:column">

  <sc-if value="{{ toast }}" hint-placeholder-val="{{ false }}">
    <div style="position:fixed; left:16px; right:16px; bottom:20px; z-index:90; display:flex; justify-content:center; pointer-events:none">
      <div style="animation:toastIn 220ms ease; background:#561530; color:#FFF9F1; border-radius:16px; padding:14px 20px; box-shadow:0 12px 40px rgba(86,21,48,0.28); display:flex; align-items:center; gap:12px; font-size:14px; max-width:420px">
        <span style="width:8px; height:8px; border-radius:999px; background:#F5AD18; flex:none"></span>
        <span>{{ toast }}</span>
      </div>
    </div>
  </sc-if>

  <sc-if value="{{ menuOpen }}" hint-placeholder-val="{{ false }}">
    <div style="position:fixed; inset:0; z-index:80; background:rgba(43,23,31,0.45); display:flex; justify-content:flex-end">
      <div style="width:min(84vw,340px); background:#FFF9F1; height:100%; padding:22px; display:flex; flex-direction:column; gap:8px; box-shadow:-20px 0 60px rgba(86,21,48,0.2)">
        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:18px; border-bottom:1px solid #EADFE2">
          <img src="<?= base_url("assets/storefront/logo.svg") ?>" alt="Dacca Delights" style="height:40px; width:auto; display:block">
          <button onClick="{{ closeMenu }}" style="width:40px; height:40px; border-radius:999px; border:1px solid #EADFE2; background:#FFFFFF; cursor:pointer; font-size:16px; color:#561530">✕</button>
        </div>
        <sc-for list="{{ navLinks }}" as="n" hint-placeholder-count="5">
          <button onClick="{{ n.go }}" style="text-align:left; background:none; border:0; padding:16px 0; border-bottom:1px solid #EADFE2; font-size:17px; font-weight:500; color:#561530; cursor:pointer">{{ n.name }}</button>
        </sc-for>
        <button onClick="{{ goMenu }}" style="margin-top:16px; background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:16px; font-size:14px; font-weight:700; cursor:pointer">Order Now</button>
        <div style="display:flex; gap:10px; margin-top:8px">
          <button onClick="{{ goAccount }}" style="flex:1; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:14px; font-size:13px; font-weight:600; color:#561530; cursor:pointer">Account</button>
          <button onClick="{{ goCart }}" style="flex:1; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:14px; font-size:13px; font-weight:600; color:#561530; cursor:pointer">Cart · {{ cartCount }}</button>
        </div>
      </div>
    </div>
  </sc-if>

  <header class="invhide" style="position:sticky; top:0; z-index:60; background:rgba(255,249,241,0.94); backdrop-filter:blur(12px); border-bottom:1px solid #EADFE2">
    <div style="max-width:1200px; margin:0 auto; padding:12px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px">
      <button onClick="{{ goHome }}" style="display:flex; align-items:center; background:none; border:0; padding:0; cursor:pointer">
        <img src="<?= base_url("assets/storefront/logo.svg") ?>" alt="Dacca Delights — An Urban Bakery Shop" style="height: 66px; width: 104px; display: block; object-fit: cover">
      </button>

      <nav class="dnav" style="align-items:center; gap:2px; flex:1; justify-content:center">
        <sc-for list="{{ navLinks }}" as="n" hint-placeholder-count="5">
          <button onClick="{{ n.go }}" style="background:none; border:0; cursor:pointer; padding:10px 13px; border-radius:999px; font-size:14px; font-weight:500; color:#561530; transition:color 160ms ease" style-hover="color:#9E1C60">{{ n.name }}</button>
        </sc-for>
      </nav>

      <div style="display:flex; align-items:center; gap:8px">
        <label class="hsearch" style="display:none; position:relative; align-items:center; gap:9px; height:42px; padding:0 14px; border-radius:12px; border:1px solid #EADFE2; background:#FFFFFF; min-width:0; transition:border-color 160ms ease" style-focus="border-color:#9E1C60">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex:none; opacity:0.6"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
          <input value="{{ query }}" onChange="{{ onQuery }}" onKeyDown="{{ onSearchKey }}" placeholder="Search breads, bagels, tarts…" aria-label="Search the menu" style="border:0; background:none; outline:none; padding:0; width:190px; min-width:0; font-size:13.5px; font-weight:500; color:#2B171F">
          <sc-if value="{{ hasQuery }}" hint-placeholder-val="{{ false }}">
            <button onClick="{{ clearQuery }}" aria-label="Clear search" style="flex:none; background:none; border:0; padding:0; cursor:pointer; color:#9C8D92; display:flex"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg></button>
          </sc-if>
          <sc-if value="{{ showSuggestHeader }}" hint-placeholder-val="{{ false }}">
            <div style="position:absolute; left:0; right:0; top:100%; margin-top:10px; z-index:70; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; overflow:hidden; box-shadow:0 18px 44px rgba(86,21,48,0.16)">
              <sc-for list="{{ suggestions }}" as="sg" hint-placeholder-count="4">
                <button onClick="{{ sg.open }}" style="width:100%; text-align:left; display:flex; align-items:center; gap:11px; background:none; border:0; border-bottom:1px solid #EADFE2; padding:10px 13px; cursor:pointer" style-hover="background:#FFF9F1">
                  <span style="position:relative; flex:none; display:block"><img src="{{ sg.image }}" alt="{{ sg.name }}" loading="lazy" style="width:38px; height:38px; border-radius:10px; object-fit:cover; display:block"><sc-if value="{{ sg.showOrigin }}" hint-placeholder-val="{{ false }}"><span title="{{ sg.originTitle }}" style="position:absolute; right:-3px; bottom:-3px; width:15px; height:15px; border-radius:999px; border:2px solid #FFFFFF; background:{{ sg.originBg }}; color:#FFFFFF; font-size:8px; font-weight:700; display:flex; align-items:center; justify-content:center; line-height:1">{{ sg.originMark }}</span></sc-if></span>
                  <span style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px">
                    <span style="font-size:13.5px; font-weight:600; color:#2B171F">{{ sg.name }}</span>
                    <span style="font-size:11px; color:#75666B">{{ sg.category }}</span>
                  </span>
                  <span style="font-size:13.5px; font-weight:700; color:#561530; white-space:nowrap">{{ sg.price }}</span>
                </button>
              </sc-for>
            </div>
          </sc-if>
        </label>

        <button onClick="{{ toggleSearch }}" aria-label="Search" class="msearchbtn" style="width:42px; height:42px; border-radius:12px; border:1px solid #EADFE2; background:#FFFFFF; cursor:pointer; color:#561530; display:flex; align-items:center; justify-content:center; transition:border-color 160ms ease, color 160ms ease" style-hover="border-color:#9E1C60; color:#9E1C60">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
        </button>

        <button onClick="{{ goCart }}" aria-label="Cart" style="width:42px; height:42px; border-radius:12px; border:1px solid #EADFE2; background:#FFFFFF; cursor:pointer; color:#561530; display:flex; align-items:center; justify-content:center; transition:border-color 160ms ease, color 160ms ease; position:relative" style-hover="border-color:#9E1C60; color:#9E1C60">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2l1.5 3"></path><path d="M18 2l-1.5 3"></path><path d="M3 6h18l-1.6 12.2a2 2 0 0 1-2 1.8H6.6a2 2 0 0 1-2-1.8L3 6z"></path><path d="M9 11v3"></path><path d="M15 11v3"></path></svg>
          <sc-if value="{{ hasCart }}" hint-placeholder-val="{{ false }}">
            <span style="position:absolute; top:-5px; right:-5px; min-width:19px; height:19px; border-radius:999px; background:#9E1C60; color:#FFFFFF; font-size:10.5px; font-weight:700; display:flex; align-items:center; justify-content:center; padding:0 5px; border:2px solid #FFF9F1">{{ cartCount }}</span>
          </sc-if>
        </button>

        <button onClick="{{ goAccount }}" aria-label="Account" style="width:42px; height:42px; border-radius:12px; border:1px solid #EADFE2; background:#FFFFFF; cursor:pointer; color:#561530; display:flex; align-items:center; justify-content:center; transition:border-color 160ms ease, color 160ms ease" style-hover="border-color:#9E1C60; color:#9E1C60">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.6"></circle><path d="M4.5 20a7.5 7.5 0 0 1 15 0"></path></svg>
        </button>

        <button onClick="{{ openMenu }}" aria-label="Open menu" style="width:42px; height:42px; border-radius:12px; border:1px solid #EADFE2; background:#FFFFFF; cursor:pointer; color:#561530; display:flex; align-items:center; justify-content:center; transition:border-color 160ms ease, color 160ms ease" style-hover="border-color:#9E1C60; color:#9E1C60">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
        </button>
      </div>
    </div>
    <sc-if value="{{ searchOpen }}" hint-placeholder-val="{{ false }}">
      <div style="border-top:1px solid #EADFE2; background:#FFFFFF">
        <div style="max-width:1200px; margin:0 auto; padding:14px 16px; position:relative">
         <div style="display:flex; gap:10px; align-items:center">
          <input value="{{ query }}" onChange="{{ onQuery }}" onKeyDown="{{ onSearchKey }}" placeholder="Search cakes, breads, pastries…" style="flex:1; min-width:0; border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:13px 16px; font-size:15px; color:#2B171F">
          <button onClick="{{ runSearch }}" style="border:0; border-radius:14px; background:#561530; color:#FFF9F1; padding:13px 20px; font-size:13px; font-weight:600; cursor:pointer">Search</button>
         </div>
          <sc-if value="{{ showSuggestHeader }}" hint-placeholder-val="{{ false }}">
            <div style="position:absolute; left:16px; right:16px; top:100%; margin-top:-2px; z-index:70; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; overflow:hidden; box-shadow:0 18px 44px rgba(86,21,48,0.16)">
              <sc-for list="{{ suggestions }}" as="sg" hint-placeholder-count="4">
                <button onClick="{{ sg.open }}" style="width:100%; text-align:left; display:flex; align-items:center; gap:11px; background:none; border:0; border-bottom:1px solid #EADFE2; padding:11px 14px; cursor:pointer" style-hover="background:#FFF9F1">
                  <span style="position:relative; flex:none; display:block"><img src="{{ sg.image }}" alt="{{ sg.name }}" loading="lazy" style="width:40px; height:40px; border-radius:11px; object-fit:cover; display:block"><sc-if value="{{ sg.showOrigin }}" hint-placeholder-val="{{ false }}"><span title="{{ sg.originTitle }}" style="position:absolute; right:-3px; bottom:-3px; width:15px; height:15px; border-radius:999px; border:2px solid #FFFFFF; background:{{ sg.originBg }}; color:#FFFFFF; font-size:8px; font-weight:700; display:flex; align-items:center; justify-content:center; line-height:1">{{ sg.originMark }}</span></sc-if></span>
                  <span style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px">
                    <span style="font-size:14px; font-weight:600; color:#2B171F">{{ sg.name }}</span>
                    <span style="font-size:11.5px; color:#75666B">{{ sg.category }}</span>
                  </span>
                  <span style="font-size:14px; font-weight:700; color:#561530; white-space:nowrap">{{ sg.price }}</span>
                </button>
              </sc-for>
            </div>
          </sc-if>
        </div>
      </div>
    </sc-if>
  </header>

  <main style="flex:1">

<?= $this->renderSection('page') ?>

  <sc-if value="{{ showMobileCart }}" hint-placeholder-val="{{ false }}">
    <div class="mcart invhide">
      <button onClick="{{ goCart }}" style="width:100%; background:#561530; color:#FFF9F1; border:0; border-radius:18px; padding:15px 18px; display:flex; align-items:center; justify-content:space-between; gap:12px; cursor:pointer; box-shadow:0 14px 34px rgba(86,21,48,0.3)">
        <span style="font-size:14px; font-weight:600">{{ cartCount }} items · {{ subtotal }}</span>
        <span style="font-size:13px; font-weight:700; color:#F5AD18">View Cart →</span>
      </button>
    </div>
  </sc-if>

  </main>

  <footer class="invhide" style="background:#561530; color:#FFF9F1; margin-top:auto">
    <div style="max-width:1200px; margin:0 auto; padding:clamp(32px,5vw,64px) 16px; display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,200px),1fr)); gap:32px">
      <div style="display:flex; flex-direction:column; gap:12px">
        
        
        <img src="<?= base_url("assets/storefront/logo.svg") ?>" alt="Dacca Delights" style="width: 143px; display: block; filter: brightness(0) invert(1); height: 114px; object-fit: cover"><p style="margin: 0; font-size: 13px; line-height: 1.7; color: rgba(255,249,241,0.7); max-width: 34ch; text-align: left; width: 223px; height: 63px">Handcrafted artisan breads, savory bakes, and gourmet pastries. Freshly baked in small batches in Dhaka.</p><div style="display:flex; align-items:center; gap:10px; margin-top:22px">
          <a href="{{ socialFacebook }}" target="_blank" rel="noopener noreferrer"
             aria-label="Dacca Delights on Facebook" title="Facebook"
             style="width:40px; height:40px; flex:none; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid rgba(245,173,24,0.45); color:#F5AD18; transition:background 160ms ease, color 160ms ease, border-color 160ms ease"
             style-hover="background:#F5AD18; border-color:#F5AD18; color:#561530">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
              <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/>
            </svg>
          </a>

          <a href="{{ socialInstagram }}" target="_blank" rel="noopener noreferrer"
             aria-label="Dacca Delights on Instagram" title="Instagram"
             style="width:40px; height:40px; flex:none; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid rgba(245,173,24,0.45); color:#F5AD18; transition:background 160ms ease, color 160ms ease, border-color 160ms ease"
             style-hover="background:#F5AD18; border-color:#F5AD18; color:#561530">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
              <rect x="2" y="2" width="20" height="20" rx="5.5"/>
              <circle cx="12" cy="12" r="4.2"/>
              <circle cx="17.6" cy="6.4" r="1.1" fill="currentColor" stroke="none"/>
            </svg>
          </a>

          <a href="{{ socialWhatsapp }}" target="_blank" rel="noopener noreferrer"
             aria-label="Message Dacca Delights on WhatsApp" title="WhatsApp"
             style="width:40px; height:40px; flex:none; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid rgba(245,173,24,0.45); color:#F5AD18; transition:background 160ms ease, color 160ms ease, border-color 160ms ease"
             style-hover="background:#F5AD18; border-color:#F5AD18; color:#561530">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0 0 20.885 3.488"/>
            </svg>
          </a>
        </div>
      </div>
      <div style="display:flex; flex-direction:column; gap:10px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#F5AD18">EXPLORE</span>
        <sc-for list="{{ navLinks }}" as="n" hint-placeholder-count="5">
          <button onClick="{{ n.go }}" style="text-align:left; background:none; border:0; padding:0; cursor:pointer; font-size:14px; color:rgba(255,249,241,0.8)" style-hover="color:#F5AD18">{{ n.name }}</button>
        </sc-for>
      </div>
      <div style="display:flex; flex-direction:column; gap:10px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#F5AD18">CUSTOMER</span>
        <button onClick="{{ goAccount }}" style="text-align:left; background:none; border:0; padding:0; cursor:pointer; font-size:14px; color:rgba(255,249,241,0.8)">My Account</button>
        <button onClick="{{ goOrders }}" style="text-align:left; background:none; border:0; padding:0; cursor:pointer; font-size:14px; color:rgba(255,249,241,0.8)">Orders</button>
        <button onClick="{{ goCart }}" style="text-align:left; background:none; border:0; padding:0; cursor:pointer; font-size:14px; color:rgba(255,249,241,0.8)">Cart</button>
        <button onClick="{{ goAbout }}" style="text-align:left; background:none; border:0; padding:0; cursor:pointer; font-size:14px; color:rgba(255,249,241,0.8)">Delivery Information</button>
      </div>
      <div style="display:flex; flex-direction:column; gap:10px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#F5AD18">CONTACT</span>
        <span style="font-size:14px; color:rgba(255,249,241,0.8)">+880 1622 823269</span>
        <span style="font-size:14px; color:rgba(255,249,241,0.8)">info@daccadelights.com</span>
        <span style="font-size:14px; color:rgba(255,249,241,0.8); line-height:1.6">Kafrul, Dhaka Cantonment, Dhaka<br></span>
        <span style="font-size:14px; color:rgba(255,249,241,0.8)">Daily 9 am – 3 pm</span>
      </div>
      <div style="display:flex; flex-direction:column; gap:12px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#F5AD18">NEWSLETTER</span>
        <span style="font-size:13px; color:rgba(255,249,241,0.7); line-height:1.6">Bake-day news and seasonal specials, once a month.</span>
        <div style="display:flex; gap:8px">
          <input placeholder="Email address" style="flex:1; min-width:0; border:1px solid rgba(255,249,241,0.25); border-radius:14px; background:rgba(255,249,241,0.06); padding:13px 14px; font-size:14px; color:#FFF9F1">
          <button onClick="{{ subscribe }}" style="border:0; border-radius:14px; background:#F5AD18; color:#561530; padding:13px 18px; font-size:13px; font-weight:700; cursor:pointer">Join</button>
        </div>
      </div>
    </div>
    <div style="border-top:1px solid rgba(255,249,241,0.16); padding:16px; text-align:center; font-size:12px; color:rgba(255,249,241,0.55)">© 2026 Dacca Delights · Dhaka, Bangladesh</div>
  </footer>

  <sc-if value="{{ chatClosed }}" hint-placeholder-val="{{ true }}">
    <button onClick="{{ openChat }}" aria-label="Chat with our order assistant" class="invhide" style="position:fixed; right:16px; bottom:{{ chatOffset }}; z-index:75; display:flex; align-items:center; gap:10px; background:#561530; color:#FFF9F1; border:0; border-radius:999px; padding:14px 20px; font-size:13.5px; font-weight:700; cursor:pointer; box-shadow:0 12px 32px rgba(86,21,48,0.32); transition:transform 160ms ease" style-hover="transform:translateY(-2px)">
      <span style="width:9px; height:9px; border-radius:999px; background:#F5AD18; flex:none"></span>
      <span>Ask us</span>
    </button>
  </sc-if>

  <sc-if value="{{ chatOpen }}" hint-placeholder-val="{{ false }}">
    <div style="position:fixed; right:0; left:0; bottom:0; top:0; z-index:78; display:flex; align-items:flex-end; justify-content:flex-end; padding:0; pointer-events:none">
      <div style="pointer-events:auto; width:min(100vw,400px); height:min(100dvh,620px); background:#FFF9F1; border:1px solid #EADFE2; border-radius:24px 24px 0 0; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 -12px 48px rgba(86,21,48,0.22); margin:0 clamp(0px,2vw,16px) 0 0">

        <div style="flex:none; background:#561530; color:#FFF9F1; padding:16px 18px; display:flex; align-items:center; gap:12px">
          <span style="width:36px; height:36px; border-radius:999px 999px 999px 6px; border:2px solid #F5AD18; flex:none"></span>
          <span style="flex:1; min-width:0; display:flex; flex-direction:column; gap:1px">
            <span style="font-family:'Fraunces',serif; font-size:17px">Order assistant</span>
            <span style="font-size:11px; color:rgba(255,249,241,0.66)">Menu, prices, delivery and orders</span>
          </span>
          <button onClick="{{ closeChat }}" aria-label="Close chat" style="width:34px; height:34px; border-radius:999px; background:rgba(255,249,241,0.12); border:0; color:#FFF9F1; font-size:14px; cursor:pointer; flex:none">✕</button>
        </div>

        <div ref="{{ chatScrollRef }}" style="flex:1; min-height:0; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px">
          <sc-if value="{{ chatEmpty }}" hint-placeholder-val="{{ true }}">
            <div style="display:flex; flex-direction:column; gap:12px">
              <p style="margin:0; font-size:14px; line-height:1.65; color:#75666B">Hello. I can talk you through the menu, check delivery to your area, and add things to your cart. What are you after today?</p>
              <div style="display:flex; flex-wrap:wrap; gap:8px">
                <sc-for list="{{ chatPrompts }}" as="p" hint-placeholder-count="4">
                  <button onClick="{{ p.send }}" style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:999px; padding:10px 15px; font-size:12.5px; font-weight:600; color:#561530; cursor:pointer; text-align:left" style-hover="border-color:#9E1C60; color:#9E1C60">{{ p.text }}</button>
                </sc-for>
              </div>
            </div>
          </sc-if>

          <sc-for list="{{ chatLog }}" as="m" hint-placeholder-count="2">
            <div style="display:flex; justify-content:{{ m.justify }}">
              <div style="max-width:86%; border-radius:{{ m.radius }}; padding:12px 15px; font-size:14px; line-height:1.6; white-space:pre-wrap; background:{{ m.bg }}; color:{{ m.fg }}; border:1px solid {{ m.border }}">{{ m.text }}</div>
            </div>
          </sc-for>

          <sc-if value="{{ chatBusy }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; align-items:center; gap:8px; padding:4px 2px">
              <span style="width:7px; height:7px; border-radius:999px; background:#9E1C60"></span>
              <span style="font-size:12.5px; color:#75666B">Checking the kitchen…</span>
            </div>
          </sc-if>
        </div>

        <div style="flex:none; border-top:1px solid #EADFE2; background:#FFFFFF; padding:12px; display:flex; gap:9px; align-items:flex-end">
          <textarea rows="1" value="{{ chatDraft }}" onChange="{{ onChatDraft }}" onKeyDown="{{ onChatKey }}" placeholder="Ask about the menu, or order something…" style="flex:1; min-width:0; border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:13px 14px; font-size:14.5px; color:#2B171F; resize:none; max-height:96px"></textarea>
          <button onClick="{{ sendChat }}" disabled="{{ chatBusy }}" aria-label="Send" style="flex:none; width:46px; height:46px; border-radius:14px; border:0; background:{{ chatSendBg }}; color:#561530; font-size:17px; font-weight:700; cursor:{{ chatSendCursor }}">↑</button>
        </div>
      </div>
    </div>
  </sc-if>
</div>
</x-dc>
<?= $this->include('storefront/_logic') ?>
</body>
</html>
