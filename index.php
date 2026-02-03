<?php
$root = __DIR__;
$uploadsUrl = 'uploads';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Signature Studio</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="bg">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
      <div class="grid"></div>
    </div>

    <main class="shell">
      <header class="hero">
        <div>
          <p class="kicker">Email Signature Tool</p>
          <h1>Signature Studio</h1>
          <p class="sub">
            Store signatures in a database, upload photos, and export a polished HTML
            signature when you're done.
          </p>
        </div>
        <div class="badge">SQLite Powered</div>
      </header>

      <section class="layout">
        <div class="panel form-panel">
          <div class="panel-head">
            <h2>Details</h2>
            <div class="chip">Stored in DB</div>
          </div>

          <form id="signatureForm" class="form" autocomplete="off">
            <input type="hidden" name="photoUrl" />
            <input type="hidden" name="bannerUrl" />
            <div class="row">
              <label>
                Full name
                <input type="text" name="fullName" placeholder="Michael Doe" />
              </label>
              <label>
                Pronouns
                <input type="text" name="pronouns" placeholder="(them • their • theirs)" />
              </label>
            </div>

            <div class="row">
              <label>
                Role line
                <input type="text" name="roleLine" placeholder="Student | Example School" />
              </label>
              <label>
                Department line
                <input type="text" name="deptLine" placeholder="School of Higher Education" />
              </label>
            </div>

            <div class="row">
              <label>
                Mobile
                <input type="tel" name="mobile" placeholder="723-364-380" />
              </label>
              <label>
                Telephone
                <input type="tel" name="telephone" placeholder="504-899-7214" />
              </label>
            </div>

            <div class="row">
              <label>
                Email
                <input type="email" name="email" placeholder="m.doe@example.edu" />
              </label>
              <label>
                Website
                <input type="text" name="website" placeholder="example.edu" />
              </label>
            </div>

            <div class="row">
              <label>
                Photo
                <input type="file" name="photo" accept="image/*" />
              </label>
              <label>
                Banner image
                <input type="file" name="banner" accept="image/*" />
              </label>
            </div>

            <div class="row">
              <label>
                Accent color
                <input type="color" name="accent" value="#2f60ff" />
              </label>
              <label>
                Theme
                <select name="theme">
                  <option value="campus">Campus Blue</option>
                  <option value="mono">Mono Minimal</option>
                  <option value="teal">Teal Pulse</option>
                </select>
              </label>
            </div>

            <label>
              Publications link
              <input type="text" name="publications" placeholder="https://example.edu/publications" />
            </label>

            <div class="panel-divider"></div>

            <p class="section-title">Social links</p>
            <div class="row">
              <label>
                LinkedIn
                <input type="url" name="linkedin" placeholder="https://linkedin.com/in/..." />
              </label>
              <label>
                X / Twitter
                <input type="url" name="x" placeholder="https://x.com/..." />
              </label>
            </div>

            <div class="row">
              <label>
                Facebook
                <input type="url" name="facebook" placeholder="https://facebook.com/..." />
              </label>
              <label>
                Instagram
                <input type="url" name="instagram" placeholder="https://instagram.com/..." />
              </label>
            </div>

            <div class="row">
              <label>
                GitHub
                <input type="url" name="github" placeholder="https://github.com/..." />
              </label>
              <label>
                YouTube
                <input type="url" name="youtube" placeholder="https://youtube.com/..." />
              </label>
            </div>

            <div class="actions">
              <button type="button" id="generateBtn">Generate</button>
              <button type="button" id="saveBtn" class="ghost">Save to DB</button>
              <button type="button" id="copyBtn">Copy HTML</button>
            </div>
          </form>
        </div>

        <div class="panel preview-panel">
          <div class="panel-head">
            <h2>Preview</h2>
            <div class="chip">Email-safe</div>
          </div>
          <div class="preview" id="previewArea"></div>

          <div class="panel-head code-head">
            <h2>Signature HTML</h2>
            <div class="chip">Copy/paste</div>
          </div>
          <pre class="code" id="htmlOutput"></pre>

          <div class="panel-head code-head">
            <h2>Saved Signatures</h2>
            <div class="chip">SQLite</div>
          </div>
          <div id="savedList" class="saved-list"></div>
        </div>
      </section>
    </main>

    <script>
      window.SIGNATURE_CONFIG = {
        uploadsUrl: "<?php echo $uploadsUrl; ?>",
      };
    </script>
    <script src="app.js"></script>
  </body>
</html>
