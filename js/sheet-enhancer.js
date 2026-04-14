(() => {
  const form = document.getElementById("sheet-form");
  if (!form) return;

  const attrFields = [
    "str_val",
    "con_val",
    "dex_val",
    "app_val",
    "pow_val",
    "int_val",
    "siz_val",
    "edu_val",
  ];
  const input = (id) => document.getElementById(id);
  const toInt = (value, fallback = 0) => {
    const n = Number(value);
    return Number.isFinite(n) ? Math.trunc(n) : fallback;
  };
  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
  const half = (n) => Math.floor(n / 2);
  const fifth = (n) => Math.floor(n / 5);
  const roll = (faces) => Math.floor(Math.random() * faces) + 1;
  const rollMany = (qty, faces) =>
    Array.from({ length: qty }, () => roll(faces)).reduce((a, b) => a + b, 0);

  const skillDefaults = [
    ["Antropologia", 1],
    ["Arcos", 15],
    ["Armas de Fogo", 20],
    ["Armas Pesadas", 10],
    ["Arqueologia", 1],
    ["Arremessar", 20],
    ["Arte e Oficio", 5],
    ["Artilharia", 1],
    ["Astronomia", 1],
    ["Atuacao", 5],
    ["Avaliacao", 5],
    ["Belas Artes", 5],
    ["Biologia", 1],
    ["Botanica", 1],
    ["Briga", 25],
    ["Cavalgar", 5],
    ["Charme", 15],
    ["Chaveiro", 1],
    ["Chicotes", 5],
    ["Ciencia", 1],
    ["Ciencia Forense", 1],
    ["Conhecimento", 1],
    ["Consertos Eletricos", 10],
    ["Consertos Mecanicos", 10],
    ["Contabilidade", 5],
    ["Criptografia", 1],
    ["Demolicoes", 1],
    ["Direito", 5],
    ["Dirigir Automoveis", 20],
    ["Disfarce", 5],
    ["Eletronica", 1],
    ["Encontrar", 25],
    ["Engenharia", 1],
    ["Escalar", 20],
    ["Espingardas", 25],
    ["Escutar", 20],
    ["Espadas", 20],
    ["Esquivar", 25],
    ["Falsificacao", 5],
    ["Farmacia", 1],
    ["Fisica", 1],
    ["Fotografia", 5],
    ["Furtividade", 20],
    ["Garrote", 15],
    ["Geologia", 1],
    ["Hipnose", 1],
    ["Historia", 5],
    ["Intimidacao", 15],
    ["Labia", 5],
    ["Lança-Chamas", 10],
    ["Lanças", 20],
    ["Leitura Labial", 1],
    ["Lingua (Nativa)", 0],
    ["Lingua (Outra)", 1],
    ["Machados", 15],
    ["Manguais", 10],
    ["Matematica", 1],
    ["Medicina", 1],
    ["Mergulho", 1],
    ["Meteorologia", 1],
    ["Metralhadoras", 10],
    ["Motosserras", 10],
    ["Mundo Natural", 10],
    ["Mythos de Cthulhu", 0],
    ["Natacao", 20],
    ["Navegacao", 10],
    ["Nivel de Credito", 0],
    ["Ocultismo", 5],
    ["Operar Maquinario Pesado", 1],
    ["Persuasao", 10],
    ["Pilotar", 1],
    ["Pistolas", 20],
    ["Prestidigitacao", 10],
    ["Primeiros Socorros", 30],
    ["Psicanalise", 1],
    ["Psicologia", 10],
    ["Quimica", 1],
    ["Rastrear", 10],
    ["Rifles", 25],
    ["Saltar", 20],
    ["Sobrevivencia", 10],
    ["Submetralhadoras", 15],
    ["Treinar Animais", 5],
    ["Usar Bibliotecas", 20],
    ["Usar Computadores", 5],
    ["Zoologia", 1],
  ];

  const occupations = [
    {
      name: "ADVOGADO",
      credit: "30-80",
      formula: "EDU*4",
      skills:
        "Contabilidade, Direito, duas interpessoais, Psicologia, Usar Bibliotecas e duas extras.",
    },
    {
      name: "ANDARILHO",
      credit: "0-5",
      formula: "EDU*2 + max(APA,DES,FOR)*2",
      skills:
        "Escalar, Escutar, Furtividade, interpessoal, Navegacao, Saltar e duas extras.",
    },
    {
      name: "ANTIQUARIO",
      credit: "30-70",
      formula: "EDU*4",
      skills:
        "Arte/Oficio, Avaliacao, Encontrar, Historia, interpessoal, Outra Lingua, Usar Bibliotecas e extra.",
    },
    {
      name: "ARTISTA",
      credit: "9-50",
      formula: "EDU*2 + max(POD,DES)*2",
      skills:
        "Arte/Oficio, Encontrar, Historia/Mundo Natural, interpessoal, Outra Lingua, Psicologia e duas extras.",
    },
    {
      name: "ATLETA",
      credit: "9-70",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Arremessar, Cavalgar, Escalar, interpessoal, Briga, Natacao, Saltar e extra.",
    },
    {
      name: "AUTOR",
      credit: "9-30",
      formula: "EDU*4",
      skills:
        "Arte Literatura, Historia, Lingua Nativa, Mundo Natural/Ocultismo, Outra Lingua, Psicologia, Bibliotecas e extra.",
    },
    {
      name: "BIBLIOTECARIO",
      credit: "9-35",
      formula: "EDU*4",
      skills:
        "Contabilidade, Lingua Nativa, Outra Lingua, Bibliotecas e quatro especialidades.",
    },
    {
      name: "CLERO",
      credit: "9-60",
      formula: "EDU*4",
      skills:
        "Contabilidade, Escutar, Historia, interpessoal, Outra Lingua, Psicologia, Bibliotecas e extra.",
    },
    {
      name: "CRIMINOSO",
      credit: "5-65",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Encontrar, Furtividade, interpessoal, Psicologia e quatro especialidades criminosas.",
    },
    {
      name: "DETETIVE PARTICULAR",
      credit: "9-30",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Fotografia, Direito, Disfarce, Encontrar, interpessoal, Psicologia, Bibliotecas e extra.",
    },
    {
      name: "DILETANTE",
      credit: "50-99",
      formula: "EDU*2 + APA*2",
      skills:
        "Armas de Fogo, Arte/Oficio, Cavalgar, interpessoal, Outra Lingua e tres extras.",
    },
    {
      name: "ENGENHEIRO",
      credit: "30-60",
      formula: "EDU*4",
      skills:
        "Desenho Tecnico, Consertos Eletricos, Ciencia Engenharia/Fisica, Consertos Mecanicos, Maquinario Pesado, Bibliotecas e extra.",
    },
    {
      name: "FANATICO",
      credit: "0-30",
      formula: "EDU*2 + max(APA,POD)*2",
      skills:
        "Furtividade, Historia, duas interpessoais, Psicologia e tres extras.",
    },
    {
      name: "FAZENDEIRO",
      credit: "9-30",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Trabalhos de Fazenda, Consertos Mecanicos, Dirigir, interpessoal, Mundo Natural, Maquinario Pesado, Rastrear e extra.",
    },
    {
      name: "HACKER",
      credit: "10-70",
      formula: "EDU*4",
      skills:
        "Consertos Eletricos, Eletronica, Encontrar, interpessoal, Computadores, Bibliotecas e duas extras.",
    },
    {
      name: "INVESTIGADOR DE POLICIA",
      credit: "20-50",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Atuar/Disfarce, Armas de Fogo, Direito, Escutar, interpessoal, Psicologia, Encontrar e extra.",
    },
    {
      name: "JORNALISTA",
      credit: "9-30",
      formula: "EDU*4",
      skills:
        "Armas de Fogo, Atuar/Disfarce, Direito, Encontrar, Escutar, interpessoal, Psicologia e extra.",
    },
    {
      name: "MEDICO",
      credit: "30-80",
      formula: "EDU*4",
      skills:
        "Biologia, Farmacia, Medicina, Outra Lingua, Primeiros Socorros, Psicologia e duas especialidades.",
    },
    {
      name: "MEMBRO DE TRIBO",
      credit: "0-15",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Arremessar/Lutar, Encontrar, Escalar, Escutar, Mundo Natural, Natacao, Ocultismo, Sobrevivencia.",
    },
    {
      name: "MISSIONARIO",
      credit: "0-30",
      formula: "EDU*4",
      skills:
        "Arte/Oficio, Consertos Mecanicos, interpessoal, Medicina, Mundo Natural, Primeiros Socorros e duas extras.",
    },
    {
      name: "MUSICO",
      credit: "9-30",
      formula: "EDU*2 + max(DES,POD)*2",
      skills: "Instrumento, Escutar, interpessoal, Psicologia e quatro extras.",
    },
    {
      name: "OFICIAL DE POLICIA",
      credit: "9-30",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Armas de Fogo, Direito, Encontrar, interpessoal, Briga, Primeiros Socorros, Psicologia e Dirigir/Cavalgar.",
    },
    {
      name: "OFICIAL MILITAR",
      credit: "20-70",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Armas de Fogo, Contabilidade, Navegacao, duas interpessoais, Psicologia, Sobrevivencia e extra.",
    },
    {
      name: "PARAPSICOLOGO",
      credit: "9-30",
      formula: "EDU*4",
      skills:
        "Antropologia, Fotografia, Historia, Bibliotecas, Ocultismo, Outra Lingua, Psicologia e extra.",
    },
    {
      name: "PILOTO",
      credit: "20-70",
      formula: "EDU*2 + DES*2",
      skills:
        "Antropologia, Fotografia, Historia, Ocultismo, Outra Lingua, Psicologia, Bibliotecas e extra.",
    },
    {
      name: "PROFESSOR",
      credit: "20-70",
      formula: "EDU*4",
      skills:
        "Lingua Nativa, Outra Lingua, Psicologia, Bibliotecas e quatro especialidades.",
    },
    {
      name: "PROFISSIONAL DE ENTRETENIMENTO",
      credit: "9-70",
      formula: "EDU*2 + APA*2",
      skills:
        "Atuar, Disfarce, Escutar, duas interpessoais, Psicologia e duas extras.",
    },
    {
      name: "SOLDADO",
      credit: "9-30",
      formula: "EDU*2 + max(DES,FOR)*2",
      skills:
        "Armas de Fogo, Escalar/Natacao, Esquivar, Furtividade, Lutar, Sobrevivencia e duas entre Mecanica/Lingua/Primeiros Socorros.",
    },
  ];

  const state = {
    manualMode: false,
    hpDirty: false,
    sanDirty: false,
    mpDirty: false,
    skills: new Map(),
  };

  function attr(name) {
    return toInt(input(name)?.value || 0, 0);
  }

  function setAttr(name, value) {
    const el = input(name);
    if (!el) return;
    el.value = String(clamp(Math.trunc(value), 1, 200));
  }

  function calcDamageAndBuild(sum) {
    if (sum <= 64) return ["-2", "-2"];
    if (sum <= 84) return ["-1", "-1"];
    if (sum <= 124) return ["0", "0"];
    if (sum <= 164) return ["+1D4", "1"];
    if (sum <= 204) return ["+1D6", "2"];
    const extraStep = Math.ceil((sum - 204) / 80);
    return [`+${1 + extraStep}D6`, String(2 + extraStep)];
  }

  function calcMove(str, dex, siz, age) {
    let mov = 8;
    if (str < siz && dex < siz) mov = 7;
    else if (str > siz && dex > siz) mov = 9;

    if (age >= 80) mov -= 5;
    else if (age >= 70) mov -= 4;
    else if (age >= 60) mov -= 3;
    else if (age >= 50) mov -= 2;
    else if (age >= 40) mov -= 1;

    return Math.max(1, mov);
  }

  function recalcDerived(fromGeneration = false) {
    const str = attr("str_val");
    const con = attr("con_val");
    const dex = attr("dex_val");
    const pow = attr("pow_val");
    const siz = attr("siz_val");
    const age = attr("age");

    const maxHp = Math.floor((con + siz) / 10);
    const maxSan = pow;
    const maxMp = Math.floor(pow / 5);
    const mov = calcMove(str, dex, siz, age);
    const [db, build] = calcDamageAndBuild(str + siz);

    if (input("max_hp")) input("max_hp").value = String(maxHp);
    if (input("max_sanity")) input("max_sanity").value = String(maxSan);
    if (input("max_magic_points"))
      input("max_magic_points").value = String(maxMp);
    if (input("move_rate")) input("move_rate").value = String(mov);
    if (input("damage_bonus")) input("damage_bonus").value = db;
    if (input("build")) input("build").value = build;

    if (fromGeneration || !state.hpDirty) input("hp").value = String(maxHp);
    if (fromGeneration || !state.sanDirty)
      input("sanity").value = String(maxSan);
    if (fromGeneration || !state.mpDirty)
      input("magic_points").value = String(maxMp);

    updateAttrFractions();
    updateOccupationPoints();
    updateSkillsComputed();
  }

  function updateAttrFractions() {
    attrFields.forEach((f) => {
      const v = attr(f);
      const halfEl = input(f.replace("_val", "_half"));
      const fifthEl = input(f.replace("_val", "_fifth"));
      if (halfEl) halfEl.textContent = String(half(v));
      if (fifthEl) fifthEl.textContent = String(fifth(v));
    });
  }

  function applyEduImprovement(edu, checks) {
    let result = edu;
    for (let i = 0; i < checks; i++) {
      const test = roll(100);
      if (test > result) {
        result += roll(10);
      }
    }
    return clamp(result, 1, 99);
  }

  function reduceFrom(arr, amount) {
    let left = amount;
    while (left > 0) {
      arr.sort((a, b) => b.value - a.value);
      arr[0].value = Math.max(1, arr[0].value - 1);
      left -= 1;
    }
  }

  function generateAttributes() {
    const age = clamp(attr("age") || 25, 15, 120);

    const attrs = {
      str_val: rollMany(3, 6) * 5,
      con_val: rollMany(3, 6) * 5,
      dex_val: rollMany(3, 6) * 5,
      app_val: rollMany(3, 6) * 5,
      pow_val: rollMany(3, 6) * 5,
      siz_val: (rollMany(2, 6) + 6) * 5,
      int_val: (rollMany(2, 6) + 6) * 5,
      edu_val: (rollMany(2, 6) + 6) * 5,
      luck: rollMany(3, 6) * 5,
    };

    if (age <= 19) {
      if (attrs.str_val >= attrs.siz_val)
        attrs.str_val = Math.max(5, attrs.str_val - 5);
      else attrs.siz_val = Math.max(5, attrs.siz_val - 5);
      attrs.edu_val = Math.max(5, attrs.edu_val - 5);
      attrs.luck = Math.max(rollMany(3, 6) * 5, rollMany(3, 6) * 5);
    } else if (age >= 20 && age < 40) {
      attrs.edu_val = applyEduImprovement(attrs.edu_val, 1);
    } else if (age >= 40 && age < 50) {
      reduceFrom(
        [
          { key: "str_val", value: attrs.str_val },
          { key: "con_val", value: attrs.con_val },
          { key: "dex_val", value: attrs.dex_val },
        ],
        5,
      );
      attrs.app_val = Math.max(5, attrs.app_val - 5);
      attrs.edu_val = applyEduImprovement(attrs.edu_val, 2);
    } else if (age >= 50 && age < 60) {
      const group = [
        { key: "str_val", value: attrs.str_val },
        { key: "con_val", value: attrs.con_val },
        { key: "dex_val", value: attrs.dex_val },
      ];
      reduceFrom(group, 10);
      attrs.str_val = group.find((x) => x.key === "str_val").value;
      attrs.con_val = group.find((x) => x.key === "con_val").value;
      attrs.dex_val = group.find((x) => x.key === "dex_val").value;
      attrs.app_val = Math.max(5, attrs.app_val - 10);
      attrs.edu_val = applyEduImprovement(attrs.edu_val, 3);
    } else if (age >= 60 && age < 70) {
      const group = [
        { key: "str_val", value: attrs.str_val },
        { key: "con_val", value: attrs.con_val },
        { key: "dex_val", value: attrs.dex_val },
      ];
      reduceFrom(group, 20);
      attrs.str_val = group.find((x) => x.key === "str_val").value;
      attrs.con_val = group.find((x) => x.key === "con_val").value;
      attrs.dex_val = group.find((x) => x.key === "dex_val").value;
      attrs.app_val = Math.max(5, attrs.app_val - 15);
      attrs.edu_val = applyEduImprovement(attrs.edu_val, 4);
    } else if (age >= 70 && age < 80) {
      const group = [
        { key: "str_val", value: attrs.str_val },
        { key: "con_val", value: attrs.con_val },
        { key: "dex_val", value: attrs.dex_val },
      ];
      reduceFrom(group, 40);
      attrs.str_val = group.find((x) => x.key === "str_val").value;
      attrs.con_val = group.find((x) => x.key === "con_val").value;
      attrs.dex_val = group.find((x) => x.key === "dex_val").value;
      attrs.app_val = Math.max(5, attrs.app_val - 20);
      attrs.edu_val = applyEduImprovement(attrs.edu_val, 4);
    } else if (age >= 80) {
      const group = [
        { key: "str_val", value: attrs.str_val },
        { key: "con_val", value: attrs.con_val },
        { key: "dex_val", value: attrs.dex_val },
      ];
      reduceFrom(group, 80);
      attrs.str_val = group.find((x) => x.key === "str_val").value;
      attrs.con_val = group.find((x) => x.key === "con_val").value;
      attrs.dex_val = group.find((x) => x.key === "dex_val").value;
      attrs.app_val = Math.max(5, attrs.app_val - 25);
      attrs.edu_val = applyEduImprovement(attrs.edu_val, 4);
    }

    Object.entries(attrs).forEach(([k, v]) => {
      if (k === "luck") return;
      setAttr(k, v);
    });

    if (input("luck_val"))
      input("luck_val").value = String(clamp(attrs.luck, 0, 99));
    state.hpDirty = false;
    state.sanDirty = false;
    state.mpDirty = false;
    recalcDerived(true);
  }

  function formulaPoints(formula, attrs) {
    const max = Math.max;
    if (formula === "EDU*4") return attrs.edu * 4;
    if (formula === "EDU*2 + max(APA,DES,FOR)*2")
      return attrs.edu * 2 + max(attrs.app, attrs.dex, attrs.str) * 2;
    if (formula === "EDU*2 + max(POD,DES)*2")
      return attrs.edu * 2 + max(attrs.pow, attrs.dex) * 2;
    if (formula === "EDU*2 + max(DES,FOR)*2")
      return attrs.edu * 2 + max(attrs.dex, attrs.str) * 2;
    if (formula === "EDU*2 + APA*2") return attrs.edu * 2 + attrs.app * 2;
    if (formula === "EDU*2 + max(APA,POD)*2")
      return attrs.edu * 2 + max(attrs.app, attrs.pow) * 2;
    if (formula === "EDU*2 + max(DES,POD)*2")
      return attrs.edu * 2 + max(attrs.dex, attrs.pow) * 2;
    if (formula === "EDU*2 + DES*2") return attrs.edu * 2 + attrs.dex * 2;
    return 0;
  }

  function setupOccupations() {
    const select = input("occupation_preset");
    const occupation = input("occupation");
    const info = input("occupation_info");
    if (!select || !occupation || !info) return;

    occupations.forEach((o) => {
      const option = document.createElement("option");
      option.value = o.name;
      option.textContent = o.name;
      select.appendChild(option);
    });

    const initial = occupation.value.trim();
    if (initial) {
      const exists = occupations.find((o) => o.name === initial.toUpperCase());
      if (exists) select.value = exists.name;
    }

    select.addEventListener("change", () => {
      const picked = occupations.find((o) => o.name === select.value);
      if (!picked) {
        info.textContent =
          "Selecione uma ocupacao para ver pericias sugeridas e Nivel de Credito.";
        return;
      }
      occupation.value = picked.name;
      info.innerHTML = `<strong>${picked.name}</strong><br>Nivel de Credito: ${picked.credit}<br>${picked.skills}`;
      updateOccupationPoints();
    });
  }

  function updateOccupationPoints() {
    const points = input("occupation_points");
    const select = input("occupation_preset");
    if (!points || !select) return;

    const picked = occupations.find((o) => o.name === select.value);
    if (!picked) {
      points.value = "-";
      return;
    }

    const attrs = {
      edu: attr("edu_val"),
      app: attr("app_val"),
      dex: attr("dex_val"),
      str: attr("str_val"),
      pow: attr("pow_val"),
    };

    points.value = String(formulaPoints(picked.formula, attrs));
  }

  function setupSkills() {
    const body = input("skills_table_body");
    const search = input("skill_search");
    const hidden = input("skills_text");
    const addBtn = input("add_custom_skill");
    if (!body || !search || !hidden || !addBtn) return;

    skillDefaults.forEach(([name, base]) => {
      state.skills.set(name, { name, base, value: base, custom: false });
    });

    const existingLines = (hidden.value || "")
      .split(/\r?\n/)
      .map((l) => l.trim())
      .filter(Boolean);
    existingLines.forEach((line) => {
      const parts = line.split(":");
      if (parts.length < 2) return;
      const name = parts[0].trim();
      const value = toInt(parts.slice(1).join(":").trim(), NaN);
      if (!name || !Number.isFinite(value)) return;
      const current = state.skills.get(name);
      if (current) {
        current.value = clamp(value, 0, 999);
      } else {
        state.skills.set(name, {
          name,
          base: 0,
          value: clamp(value, 0, 999),
          custom: true,
        });
      }
    });

    function specialBases() {
      const dex = attr("dex_val");
      const edu = attr("edu_val");
      const esquivar = state.skills.get("Esquivar");
      if (esquivar && !esquivar.custom) esquivar.base = Math.floor(dex / 2);
      const native = state.skills.get("Lingua (Nativa)");
      if (native && !native.custom) native.base = clamp(edu, 1, 99);
    }

    function renderSkills() {
      specialBases();
      const term = search.value.trim().toLowerCase();
      const list = Array.from(state.skills.values())
        .filter((s) => s.name.toLowerCase().includes(term))
        .sort((a, b) => a.name.localeCompare(b.name));

      body.innerHTML = "";
      list.forEach((skill) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${skill.name}${skill.custom ? " <small>(custom)</small>" : ""}</td>
          <td>${skill.base}</td>
          <td><input type="number" min="0" max="999" value="${skill.value}" data-skill="${skill.name.replace(/"/g, "&quot;")}" class="skill-value"></td>
          <td>${Math.floor(skill.value / 2)}</td>
          <td>${Math.floor(skill.value / 5)}</td>
        `;
        body.appendChild(tr);
      });

      body.querySelectorAll(".skill-value").forEach((el) => {
        el.addEventListener("input", () => {
          const name = el.getAttribute("data-skill");
          const rec = state.skills.get(name);
          if (!rec) return;
          rec.value = clamp(toInt(el.value, rec.base), 0, 999);
          serializeSkills();
          renderSkills();
        });
      });

      serializeSkills();
    }

    function serializeSkills() {
      const lines = Array.from(state.skills.values())
        .filter((s) => s.value > 0)
        .sort((a, b) => a.name.localeCompare(b.name))
        .map((s) => `${s.name}: ${s.value}`);
      hidden.value = lines.join("\n");
    }

    addBtn.addEventListener("click", () => {
      const name = prompt("Nome da nova pericia:");
      if (!name) return;
      const clean = name.trim();
      if (!clean) return;
      if (!state.skills.has(clean)) {
        state.skills.set(clean, {
          name: clean,
          base: 0,
          value: 0,
          custom: true,
        });
      }
      renderSkills();
      search.value = clean;
      renderSkills();
    });

    search.addEventListener("input", renderSkills);

    state.renderSkills = renderSkills;
    renderSkills();
  }

  function wireEvents() {
    input("generate_attributes")?.addEventListener("click", generateAttributes);

    input("toggle_manual_mode")?.addEventListener("click", (e) => {
      state.manualMode = !state.manualMode;
      attrFields.forEach((id) => {
        const el = input(id);
        if (el) el.readOnly = !state.manualMode;
      });
      e.currentTarget.textContent = state.manualMode
        ? "Modo manual: ATIVADO"
        : "Modo manual: DESATIVADO";
    });

    input("hp")?.addEventListener("input", () => {
      state.hpDirty = true;
    });
    input("sanity")?.addEventListener("input", () => {
      state.sanDirty = true;
    });
    input("magic_points")?.addEventListener("input", () => {
      state.mpDirty = true;
    });

    ["age", ...attrFields].forEach((id) => {
      input(id)?.addEventListener("input", () => {
        recalcDerived(false);
      });
    });

    form.addEventListener("submit", () => {
      if (typeof state.renderSkills === "function") {
        state.renderSkills();
      }
    });
  }

  function initialSync() {
    attrFields.forEach((id) => {
      const el = input(id);
      if (el) el.readOnly = true;
    });
    recalcDerived(false);
    updateAttrFractions();
  }

  setupOccupations();
  setupSkills();
  wireEvents();
  initialSync();
})();
