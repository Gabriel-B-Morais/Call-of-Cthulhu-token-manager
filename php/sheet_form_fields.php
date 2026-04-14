<nav class="section-nav">
  <a href="#identidade">Identidade</a>
  <a href="#atributos">Atributos</a>
  <a href="#derivados">Derivados</a>
  <a href="#pericias">Pericias</a>
  <a href="#anotacoes">Anotacoes</a>
</nav>

<section id="identidade" class="sheet-block">
  <h2 class="section-title">Identidade e Ocupacao</h2>
  <div class="grid-3">
    <div class="containerInput">
      <label for="character_name">Nome do personagem</label>
      <input type="text" id="character_name" name="character_name" required maxlength="80" value="<?php echo htmlspecialchars((string) ($data['character_name'] ?? '')); ?>">
    </div>
    <div class="containerInput">
      <label for="age">Idade</label>
      <input type="number" id="age" name="age" min="15" max="120" value="<?php echo htmlspecialchars((string) ($data['age'] ?? '25')); ?>">
    </div>
    <div class="containerInput">
      <label for="luck_val">Sorte (calculada)</label>
      <input type="number" id="luck_val" min="0" max="99" value="" readonly>
    </div>
  </div>

  <div class="grid-3">
    <div class="containerInput">
      <label for="occupation_preset">Ocupacao pronta</label>
      <select id="occupation_preset">
        <option value="">Selecione uma ocupacao</option>
      </select>
    </div>
    <div class="containerInput">
      <label for="occupation">Ocupacao final (editavel)</label>
      <input type="text" id="occupation" name="occupation" maxlength="80" value="<?php echo htmlspecialchars((string) ($data['occupation'] ?? '')); ?>">
    </div>
    <div class="containerInput">
      <label for="occupation_points">Pontos ocupacionais</label>
      <input type="text" id="occupation_points" value="-" readonly>
    </div>
  </div>

  <div class="panel" id="occupation_info">Selecione uma ocupacao para ver pericias sugeridas e Nivel de Credito.</div>

  <div class="grid-2">
    <div class="containerInput">
      <label for="residence">Residencia</label>
      <input type="text" id="residence" name="residence" maxlength="120" value="<?php echo htmlspecialchars((string) ($data['residence'] ?? '')); ?>">
    </div>
    <div class="containerInput">
      <label for="birthplace">Local de nascimento</label>
      <input type="text" id="birthplace" name="birthplace" maxlength="120" value="<?php echo htmlspecialchars((string) ($data['birthplace'] ?? '')); ?>">
    </div>
  </div>
</section>

<section id="atributos" class="sheet-block">
  <h2 class="section-title">Atributos</h2>
  <div class="news-actions">
    <button type="button" class="btn" id="generate_attributes">Gerar atributos automaticamente</button>
    <button type="button" class="btn btn-outline" id="toggle_manual_mode">Modo manual: DESATIVADO</button>
  </div>
  <p class="muted">Geracao segue regras por idade. No modo manual, voce pode digitar os atributos diretamente.</p>

  <div class="table-wrap">
    <table class="calc-table">
      <thead>
        <tr>
          <th>Atributo</th>
          <th>Total</th>
          <th>1/2</th>
          <th>1/5</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $attrMap = [
          'str_val' => 'FOR',
          'con_val' => 'CON',
          'dex_val' => 'DES',
          'app_val' => 'APA',
          'pow_val' => 'POD',
          'int_val' => 'INT',
          'siz_val' => 'TAM',
          'edu_val' => 'EDU',
        ];
        foreach ($attrMap as $field => $label):
          $value = (int) ($data[$field] ?? 50);
          $halfId = str_replace('_val', '_half', $field);
          $fifthId = str_replace('_val', '_fifth', $field);
        ?>
          <tr>
            <td><strong><?php echo $label; ?></strong></td>
            <td>
              <input type="number" id="<?php echo $field; ?>" name="<?php echo $field; ?>" min="1" max="200" value="<?php echo $value; ?>" class="attr-input" data-attr-label="<?php echo $label; ?>">
            </td>
            <td><span id="<?php echo $halfId; ?>">0</span></td>
            <td><span id="<?php echo $fifthId; ?>">0</span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section id="derivados" class="sheet-block">
  <h2 class="section-title">Atributos Derivados e Combate</h2>
  <div class="grid-3">
    <div class="containerInput"><label for="hp">PV Atual</label><input type="number" id="hp" name="hp" min="0" max="99" value="<?php echo htmlspecialchars((string) ($data['hp'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="max_hp">PV Max</label><input type="number" id="max_hp" name="max_hp" min="0" max="99" readonly value="<?php echo htmlspecialchars((string) ($data['max_hp'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="sanity">SAN Atual</label><input type="number" id="sanity" name="sanity" min="0" max="999" value="<?php echo htmlspecialchars((string) ($data['sanity'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="max_sanity">SAN Max</label><input type="number" id="max_sanity" name="max_sanity" min="0" max="999" readonly value="<?php echo htmlspecialchars((string) ($data['max_sanity'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="magic_points">PM Atual</label><input type="number" id="magic_points" name="magic_points" min="0" max="99" value="<?php echo htmlspecialchars((string) ($data['magic_points'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="max_magic_points">PM Max</label><input type="number" id="max_magic_points" name="max_magic_points" min="0" max="99" readonly value="<?php echo htmlspecialchars((string) ($data['max_magic_points'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="move_rate">MOV</label><input type="number" id="move_rate" name="move_rate" min="1" max="20" readonly value="<?php echo htmlspecialchars((string) ($data['move_rate'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="damage_bonus">Dano Extra</label><input type="text" id="damage_bonus" name="damage_bonus" readonly value="<?php echo htmlspecialchars((string) ($data['damage_bonus'] ?? '')); ?>"></div>
    <div class="containerInput"><label for="build">Corpo</label><input type="text" id="build" name="build" readonly value="<?php echo htmlspecialchars((string) ($data['build'] ?? '')); ?>"></div>
  </div>
</section>

<section id="pericias" class="sheet-block">
  <h2 class="section-title">Pericias</h2>
  <p class="muted">Pesquise, ajuste os valores e adicione pericias personalizadas. Metade e um quinto sao calculados automaticamente.</p>
  <input type="hidden" id="skills_text" name="skills_text" value="<?php echo htmlspecialchars((string) ($data['skills_text'] ?? '')); ?>">

  <div class="form-toolbar">
    <input type="text" id="skill_search" placeholder="Buscar pericia...">
    <div class="news-actions">
      <button type="button" class="btn btn-outline" id="add_custom_skill">Adicionar pericia</button>
    </div>
  </div>

  <div class="table-wrap skills-wrap">
    <table class="skills-table">
      <thead>
        <tr>
          <th>Pericia</th>
          <th>Base</th>
          <th>Total</th>
          <th>1/2</th>
          <th>1/5</th>
        </tr>
      </thead>
      <tbody id="skills_table_body"></tbody>
    </table>
  </div>
</section>

<section id="anotacoes" class="sheet-block">
  <h2 class="section-title">Equipamentos e Notas</h2>
  <div class="containerInput">
    <label for="equipment_text">Equipamentos</label>
    <textarea id="equipment_text" name="equipment_text" rows="5"><?php echo htmlspecialchars((string) ($data['equipment_text'] ?? '')); ?></textarea>
  </div>

  <div class="containerInput">
    <label for="notes_text">Notas da sessao</label>
    <textarea id="notes_text" name="notes_text" rows="8"><?php echo htmlspecialchars((string) ($data['notes_text'] ?? '')); ?></textarea>
  </div>
</section>