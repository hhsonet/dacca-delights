<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isAuth }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(16px,3vw,40px) 16px clamp(36px,5vw,72px)">
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr)); gap:clamp(20px,3vw,40px); align-items:stretch">
        <div class="authpic" style="position:relative; border-radius:28px; overflow:hidden; min-height:520px; background:#561530">
          <img src="https://www.daccadelights.com/assets/Items/Bread.jpeg" alt="Freshly baked bread" loading="lazy" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0.55">
          <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(86,21,48,0.25), rgba(86,21,48,0.92))"></div>
          <div style="position:absolute; left:0; right:0; bottom:0; padding:34px; display:flex; flex-direction:column; gap:12px; color:#FFF9F1">
            <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#F5AD18">AN URBAN BAKERY SHOP</span>
            <span style="font-family:'Fraunces',serif; font-size:38px; line-height:1.08">Ready for Freshly Baked Delights?</span>
            <span style="font-size:14px; line-height:1.7; color:rgba(255,249,241,0.75); max-width:38ch">Sign in to reorder your daily favorites, track live deliveries, and save your preferences for next time.</span>
          </div>
        </div>
        <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:28px; padding:clamp(24px,4vw,40px); display:flex; flex-direction:column; gap:16px; justify-content:center">
          <img src="<?= base_url("assets/storefront/logo.svg") ?>" alt="Dacca Delights" style="height: 129px; width: 152px; align-self: flex-start; object-fit: cover">
          <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(30px,4vw,40px); margin:0; color:#561530">{{ authTitle }}</h1>
          <p style="margin: 0 0 4px; font-size: 12px; color: #75666B">{{ authBlurb }}</p>

          <sc-if value="{{ otpStep }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:16px">
              <div style="background:#FFF9F1; border-left:3px solid #F5AD18; border-radius:0 12px 12px 0; padding:13px 16px; display:flex; flex-direction:column; gap:3px">
                <span style="font-size:13px; font-weight:700; color:#561530">Code sent to {{ otpTarget }}</span>
                <span style="font-size:12.5px; line-height:1.6; color:#75666B">Enter the 6-digit code to verify your account. Demo code: <strong style="color:#811844">{{ otpDemo }}</strong></span>
              </div>
              <div style="display:flex; gap:8px; justify-content:space-between">
                <sc-for list="{{ otpBoxes }}" as="b" hint-placeholder-count="6">
                  <input ref="{{ b.ref }}" value="{{ b.value }}" onChange="{{ b.onChange }}" onKeyDown="{{ b.onKey }}" inputMode="numeric" maxLength="1" aria-label="Digit {{ b.n }}" style="flex:1; min-width:0; text-align:center; border:1.5px solid {{ b.border }}; border-radius:12px; background:#FFF9F1; padding:15px 0; font-family:'Plus Jakarta Sans',sans-serif; font-size:20px; font-weight:700; color:#561530">
                </sc-for>
              </div>
              <sc-if value="{{ otpError }}" hint-placeholder-val="{{ false }}">
                <span style="font-size:12.5px; font-weight:600; color:#B3261E">{{ otpError }}</span>
              </sc-if>
              <button onClick="{{ verifyOtp }}" disabled="{{ otpIncomplete }}" style="background:{{ otpBtnBg }}; color:#561530; border:0; border-radius:16px; padding:17px; font-size:15px; font-weight:700; cursor:{{ otpCursor }}">Verify &amp; Continue</button>
              <div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; align-items:center">
                <button onClick="{{ resendOtp }}" disabled="{{ resendWait }}" style="background:none; border:0; padding:0; cursor:pointer; font-size:13px; font-weight:600; color:{{ resendColor }}">{{ resendLabel }}</button>
                <button onClick="{{ cancelOtp }}" style="background:none; border:0; padding:0; cursor:pointer; font-size:13px; font-weight:600; color:#75666B">Change {{ otpTargetKind }}</button>
              </div>
            </div>
          </sc-if>

          <sc-if value="{{ formStep }}" hint-placeholder-val="{{ true }}">

          <sc-if value="{{ phoneStep }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:12px; background:#FFF9F1; border:1px solid #EADFE2; border-radius:18px; padding:18px">
              <div style="display:flex; align-items:baseline; justify-content:space-between; gap:12px">
                <span style="font-size:13.5px; font-weight:700; color:#561530">Your mobile number</span>
                <button onClick="{{ closePhoneStep }}" style="background:none; border:0; padding:0; cursor:pointer; font-size:12.5px; font-weight:600; color:#75666B">Cancel</button>
              </div>
              <span style="display:flex; align-items:stretch">
                <span style="display:flex; align-items:center; padding:0 14px; border:1px solid #EADFE2; border-right:0; border-radius:14px 0 0 14px; background:#F3ECE6; font-size:14px; font-weight:700; color:#561530">+880</span>
                <input type="tel" inputMode="numeric" maxLength="10" value="{{ form.phone }}" onChange="{{ form.setphone }}" placeholder="17XXXXXXXX" style="border:1px solid #EADFE2; border-radius:0 14px 14px 0; background:#FFFFFF; padding:15px; font-size:15px; color:#2B171F; width:100%; min-width:0">
              </span>
              <sc-if value="{{ err.phone }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.phone }}</span></sc-if>
              <span style="font-size:12px; line-height:1.6; color:#75666B">We'll send a one-time code by SMS or WhatsApp. No password needed.</span>
              <button onClick="{{ sendPhoneOtp }}" style="background:#F5AD18; color:#561530; border:0; border-radius:14px; padding:15px; font-size:14px; font-weight:700; cursor:pointer">Send Code</button>
            </div>
          </sc-if>

          <button onClick="{{ continueGoogle }}" style="display:flex; align-items:center; justify-content:center; gap:11px; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:16px; font-size:14px; font-weight:600; color:#2B171F; cursor:pointer; transition:border-color 160ms ease, box-shadow 160ms ease" style-hover="border-color:#561530; box-shadow:0 4px 14px rgba(86,21,48,0.08)">
            <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true" style="flex:none"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.6l6.7-6.7C35.6 2.6 30.2 0 24 0 14.6 0 6.5 5.4 2.6 13.2l7.8 6.1C12.3 13.3 17.6 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.1 24.5c0-1.6-.15-3.2-.43-4.7H24v9h12.4c-.54 2.9-2.2 5.3-4.6 6.9l7.6 5.9c4.4-4.1 6.7-10.1 6.7-17.1z"></path><path fill="#FBBC05" d="M10.4 28.7A14.5 14.5 0 0 1 9.6 24c0-1.6.3-3.2.8-4.7l-7.8-6.1A24 24 0 0 0 0 24c0 3.9.9 7.5 2.6 10.8l7.8-6.1z"></path><path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.6-5.9c-2.1 1.4-4.9 2.3-8.3 2.3-6.4 0-11.7-3.8-13.6-9.2l-7.8 6.1C6.5 42.6 14.6 48 24 48z"></path></svg>
            <span>Continue with Google</span>
          </button>

          <button onClick="{{ continueWhatsapp }}" style="display:flex; align-items:center; justify-content:center; gap:11px; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:16px; font-size:14px; font-weight:600; color:#2B171F; cursor:pointer; transition:border-color 160ms ease, box-shadow 160ms ease" style-hover="border-color:#561530; box-shadow:0 4px 14px rgba(86,21,48,0.08)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366" aria-hidden="true" style="flex:none"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.8 14.13c-.25.7-1.44 1.33-1.98 1.38-.54.06-1.02.1-1.94-.24-1.11-.42-3.6-1.66-5.07-4.1-.62-1.03-.86-1.85-.9-2.53-.04-.7.35-1.28.6-1.55.24-.26.51-.32.68-.32.18 0 .35 0 .5.01.16.01.38-.06.58.45.2.5.7 1.72.76 1.84.06.12.1.27.02.43-.08.16-.15.26-.3.43-.14.16-.3.36-.43.48-.14.14-.29.29-.13.57.16.28.71 1.18 1.53 1.91 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.6-.07.16-.2.7-.83.89-1.11.19-.29.37-.24.63-.15.25.09 1.6.76 1.87.9.28.14.46.21.53.33.07.12.07.7-.18 1.4z"></path></svg>
            <span>Continue with Phone / WhatsApp</span>
          </button>

          <div style="display:flex; align-items:center; gap:12px; padding:2px 0">
            <span style="flex:1; height:1px; background:#EADFE2"></span>
            <span style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:#A79A9E">OR {{ authModeWord }} WITH EMAIL</span>
            <span style="flex:1; height:1px; background:#EADFE2"></span>
          </div>

          <sc-if value="{{ isRegister }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:6px">
              <input type="text" value="{{ form.name }}" onChange="{{ form.setname }}" placeholder="Full name" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F; width:100%">
              <sc-if value="{{ err.name }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.name }}</span></sc-if>
            </div>
          </sc-if>
          <div style="display:flex; flex-direction:column; gap:6px">
            <input type="email" value="{{ form.email }}" onChange="{{ form.setemail }}" placeholder="Email address" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F; width:100%">
            <sc-if value="{{ err.email }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.email }}</span></sc-if>
          </div>
          <sc-if value="{{ isRegister }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:6px">
              <span style="display:flex; align-items:stretch">
                <span style="display:flex; align-items:center; padding:0 14px; border:1px solid #EADFE2; border-right:0; border-radius:14px 0 0 14px; background:#F3ECE6; font-size:14px; font-weight:700; color:#561530">+880</span>
                <input type="tel" inputMode="numeric" maxLength="10" value="{{ form.phone }}" onChange="{{ form.setphone }}" placeholder="17XXXXXXXX" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F; width:100%; border-radius:0 14px 14px 0; min-width:0">
              </span>
              <sc-if value="{{ err.phone }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.phone }}</span></sc-if>
            </div>
          </sc-if>
          <div style="display:flex; flex-direction:column; gap:6px">
            <div style="position:relative; display:flex; align-items:center">
              <input type="{{ pwType }}" value="{{ form.pw }}" onChange="{{ form.setpw }}" placeholder="Password" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px 76px 15px 15px; font-size:15px; color:#2B171F; width:100%">
              <button onClick="{{ togglePw }}" style="position:absolute; right:8px; background:none; border:0; cursor:pointer; font-size:12px; font-weight:600; color:#811844; padding:8px">{{ pwToggleLabel }}</button>
            </div>
            <sc-if value="{{ err.pw }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.pw }}</span></sc-if>
          </div>

          <sc-if value="{{ isRegister }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:10px">
              <div style="display:flex; flex-direction:column; gap:6px">
                <input type="password" value="{{ form.pw2 }}" onChange="{{ form.setpw2 }}" placeholder="Confirm password" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F; width:100%">
                <sc-if value="{{ err.pw2 }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.pw2 }}</span></sc-if>
              </div>
              <div style="display:flex; flex-direction:column; gap:6px">
                <div style="height:6px; border-radius:999px; background:#EADFE2; overflow:hidden">
                  <div style="height:100%; border-radius:999px; width:{{ pwStrengthPct }}; background:{{ pwStrengthColor }}; transition:width 220ms ease"></div>
                </div>
                <span style="font-size:12px; color:#75666B">Password strength: <strong style="color:{{ pwStrengthColor }}">{{ pwStrengthLabel }}</strong></span>
              </div>
              <button onClick="{{ toggleTerms }}" style="display:flex; align-items:center; gap:10px; background:none; border:0; padding:4px 0; cursor:pointer; text-align:left">
                <span style="width:20px; height:20px; border-radius:6px; border:1.5px solid {{ termsBorder }}; background:{{ termsBg }}; color:#561530; font-size:13px; display:flex; align-items:center; justify-content:center; flex:none">{{ termsMark }}</span>
                <span style="font-size:13px; color:#75666B">I agree to the Terms &amp; Conditions and Privacy Policy</span>
              </button>
              <sc-if value="{{ err.terms }}" hint-placeholder-val="{{ false }}"><span style="font-size:12px; color:#B3261E">{{ err.terms }}</span></sc-if>
            </div>
          </sc-if>

          <sc-if value="{{ isLogin }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
              <button onClick="{{ toggleRemember }}" style="display:flex; align-items:center; gap:10px; background:none; border:0; padding:0; cursor:pointer">
                <span style="width:20px; height:20px; border-radius:6px; border:1.5px solid {{ rememberBorder }}; background:{{ rememberBg }}; color:#561530; font-size:13px; display:flex; align-items:center; justify-content:center">{{ rememberMark }}</span>
                <span style="font-size:13px; color:#75666B">Remember me</span>
              </button>
              <button onClick="{{ forgotPw }}" style="background:none; border:0; cursor:pointer; font-size:13px; font-weight:600; color:#811844; padding:0">Forgot password?</button>
            </div>
          </sc-if>

          <button onClick="{{ submitAuth }}" style="background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:17px; font-size:15px; font-weight:700; cursor:pointer; transition:transform 160ms ease" style-hover="transform:translateY(-1px)">{{ authCta }}</button>
          </sc-if>
          <div style="display:flex; justify-content:center; gap:6px; font-size:13px; color:#75666B">
            <span>{{ authSwitchLead }}</span>
            <button onClick="{{ toggleAuth }}" style="background:none; border:0; cursor:pointer; font-size:13px; font-weight:700; color:#9E1C60; padding:0">{{ authSwitchCta }}</button>
          </div>
        </div>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
