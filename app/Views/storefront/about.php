<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isAbout }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(20px,4vw,56px) 16px clamp(32px,4vw,64px); display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:clamp(24px,4vw,52px); align-items:center">
      <div style="display:flex; flex-direction:column; gap:16px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#9E1C60">OUR STORY</span>
        <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(34px,6vw,58px); margin:0; color:#561530; line-height:1.03">Started small. Now rising like fresh dough in Dhaka.</h1>
        <p style="margin:0; font-size:15px; line-height:1.75; color:#75666B; text-wrap:pretty">Dacca Delights began it's journey small, with a notebook of WhatsApp and Facebook Messenger orders, and a simple belief: good baking takes time. Today, we’re a cloud kitchen in Dhaka, still keeping things small and intentional. We bake in batches, work with dough and recipes from scratch, and prepare what’s ordered, so every bake reaches you at its freshest.</p>
        <p style="margin:0; font-size:15px; line-height:1.75; color:#75666B; text-wrap:pretty">Our menu is deliberately focused: breads, pastries, muffins, cookies and desserts, each made in-house with the time and care it deserves.  No storefront. No unnecessary batches. Just fresh baking, made for your order and delivered to your door.</p>
      </div>
      <div style="aspect-ratio:4/5; border-radius:28px; overflow:hidden; background:#F3E7D6">
        <img src="{{ aboutImage }}" alt="Baker at work" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block">
      </div>
    </section>
    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(36px,5vw,72px)">
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,220px),1fr)); gap:16px">
        <sc-for list="{{ contactCards }}" as="c" hint-placeholder-count="3">
          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:8px">
            <span style="font-size:11px; font-weight:700; letter-spacing:0.18em; color:#9E1C60">{{ c.label }}</span><span style="font-size:15px; line-height:1.65; color:#2B171F">{{ c.value }}</span>
          </div>
        </sc-for>

        <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:14px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.18em; color:#9E1C60">SOCIAL</span>
          <div style="display:flex; align-items:center; gap:10px">

            <a href="{{ socialFacebook }}" target="_blank" rel="noopener noreferrer"
               aria-label="Dacca Delights on Facebook" title="Facebook"
               style="width:44px; height:44px; flex:none; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid #EADFE2; background:#FFF9F1; color:#561530; transition:background 160ms ease, color 160ms ease, border-color 160ms ease"
               style-hover="background:#561530; border-color:#561530; color:#FFF9F1">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/>
              </svg>
            </a>

            <a href="{{ socialInstagram }}" target="_blank" rel="noopener noreferrer"
               aria-label="Dacca Delights on Instagram" title="Instagram"
               style="width:44px; height:44px; flex:none; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid #EADFE2; background:#FFF9F1; color:#561530; transition:background 160ms ease, color 160ms ease, border-color 160ms ease"
               style-hover="background:#561530; border-color:#561530; color:#FFF9F1">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <rect x="2" y="2" width="20" height="20" rx="5.5"/>
                <circle cx="12" cy="12" r="4.2"/>
                <circle cx="17.6" cy="6.4" r="1.1" fill="currentColor" stroke="none"/>
              </svg>
            </a>

            <a href="{{ socialWhatsapp }}" target="_blank" rel="noopener noreferrer"
               aria-label="Message Dacca Delights on WhatsApp" title="WhatsApp"
               style="width:44px; height:44px; flex:none; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid #EADFE2; background:#FFF9F1; color:#561530; transition:background 160ms ease, color 160ms ease, border-color 160ms ease"
               style-hover="background:#561530; border-color:#561530; color:#FFF9F1">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0 0 20.885 3.488"/>
              </svg>
            </a>

          </div>
        </div>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
