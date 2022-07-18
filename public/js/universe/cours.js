//Dactylo lesson

$(function () {
    TypingTrainer = function () {
      //Constructor
      this.alphabet =
        'yyyzzz0123456789abcdefghijklmnopqrstuvwxyz';
      this.keys =
        ':;\\\'!@#$%^&*()[]{}`~\/|/|_+=-/?>';
      this.lection = '';
      this.lectionText = '';
      this.score = {
        hits: 0,
        misses: 0,
        lastCheckLength: 0
      };
      this.config = {
        bufferSize: 25,
        bufferReluctanceFactor: 3,
        eventHolder: $('body'),
        lesson: 'random-full'
  
      };
    };
    //populate lesson buffer
    TypingTrainer.prototype.generateRandomLetter =
      function () {
        var consider = this.keys;
        if (this.config.lesson ==
          'random-full') {
          console.log(
            "alphabet");
          consider += this.alphabet;
        } else if (this.config.lesson ==
          'random-letter') {
          consider = this.alphabet;
        } else if (this.config.lesson ==
          'random-yz') {
          consider = 'zy';
        } else if (this.config.lesson ==
          'random-homerow') {
          consider =
            'asdfghjkl;\':"|\\';
        } else if (this.config.lesson ==
          'random-homerow-bottomrow'
        ) {
          consider =
            '\\||zxcvbnm,./<>?/asdfghjkl;\':"|\\';
        } else if (this.config.lesson ==
          'random-homerow-toprow'
        ) {
          consider =
            'qwertyuiop[]{}asdfghjkl;\':"|\\';
        } else if (this.config.lesson ==
          'random-bottomrow') {
          consider =
            '\\||zxcvbnm,./<>?/';
        } else if (this.config.lesson ==
          'random-toprow') {
          consider =
            'qwertyuiop[]{}';
        } else if (this.config.lesson ==
          'random-numberspechials'
        ) {
          consider =
            '!@#$%^&*()_+=-';
        }
  
  
        return consider[Math.ceil(
            Math.random() *
            consider.length
          ) -
          1];
    };
  
    TypingTrainer.prototype.clearLection =
      function () {
        this.lection = '';
        this.lectionText = '';
        this.config.eventHolder
          .trigger(
            'tt.lection_update'
        );
    };
    TypingTrainer.prototype.clearScore =
      function () {
        this.score.hits = 0;
        this.score.misses = 0;
        this.config.eventHolder
          .trigger(
            'tt.score_update'
        );
  
    };
  
    TypingTrainer.prototype.populateLectionBuffer =
      function () {
        if (/random-/g.test(
          this.config.lesson
        )) {
          while (this.lection
            .length < this.config
            .bufferSize) {
            this.lection =
              this.lection +
              this.generateRandomLetter();
            this.config.eventHolder
              .trigger(
                'tt.lection_update'
            );
          }
        } else if (this.config.lesson =
          'wikipedia') {
  
          if (this.lectionText
            .length < (this
              .config.bufferReluctanceFactor *
              this.config
              .lesson.length
            )) {
            var _self =
              this;
            //TODO: a lot of closed world assumptions here
            $.ajax({
              url: "https://en.wikipedia.org/w/api.php?action=query&list=random&rnnamespace=0&rnlimit=1&format=json",
              cache: false,
              dataType: 'jsonp',
              success: function (
                data
              ) {
                var ida =
                  data
                  .query
                  .random[
                    0
                ].id;
                $.ajax({
                  url: "https://en.wikipedia.org/w/api.php?action=query&prop=extracts&exchars=700&format=json&explaintext&pageids=" + ida,
                  cache: false,
                  dataType: 'jsonp',
                  success: function (
                    data
                  ) {
  
                    //remove all non ascii chars
                    var newLection =
                      data
                      .query
                      .pages[
                        ida
                    ]
                      .extract;
                    newLection =
                      newLection
                      .replace(
                        /[\u0080-\uffff]/g,
                        ""
                    )
                      .replace(
                        /(\r\n|\n|\r)/gm,
                        ""
                    );
  
                    _self
                      .lectionText +=
                      newLection;
                    console
                      .log(
                        data
                        .query
                        .pages[
                          ida
                        ]
                        .extract
                    );
                    while (
                      _self
                      .lection
                      .length <
                      _self
                      .config
                      .bufferSize
                    ) {
  
                      //console.log(i,_self.lection);
                      _self
                        .lection =
                        _self
                        .lectionText
                        .slice(
                          0,
                          Math
                          .min(
                            _self
                            .config
                            .bufferSize,
                            _self
                            .lectionText
                            .length
                          )
                      );
                      _self
                        .config
                        .eventHolder
                        .trigger(
                          'tt.lection_update'
                      );
                    }
                  }
                });
              }
            });
          }
  
          while (this.lection
            .length < this.config
            .bufferSize &&
            this.lectionText
            .length > this.config
            .bufferSize) {
            this.lection =
              this.lectionText
              .slice(0,
                Math.min(
                  this
                  .config
                  .bufferSize,
                  this
                  .lectionText
                  .length
                ));
            this.config.eventHolder
              .trigger(
                'tt.lection_update'
            );
          }
        }
  
    };
  
    TypingTrainer.prototype.generateLection =
      function () {
        this.clearLection();
        this.populateLectionBuffer();
    };
  
    TypingTrainer.prototype.reloadLection =
      function (type) {
        this.config.lesson =
          type;
        this.generateLection();
    };
  
    TypingTrainer.prototype.tryRemove =
      function (input) {
  
        //only consider first char
        if (input[0] == this.lection[
          0]) {
          this.lection = this
            .lection.slice(
              1, this.lection
              .length);
          this.lectionText =
            this.lectionText
            .slice(1, this.lectionText
              .length);
          this.score.hits++;
          this.config.eventHolder
            .trigger(
              'tt.score_update'
          );
          this.populateLectionBuffer();
          this.score.lastCheckLength =
            input.length;
          return input.slice(
            1, input.length
          );
        }
        console.log(this.score.lastCheckLength,
          input.length)
        if (this.score.lastCheckLength <
          input.length) {
          this.score.misses +=
            input.length -
            this.score.lastCheckLength;
          this.config.eventHolder
            .trigger(
              'tt.score_update'
          );
        }
        this.score.lastCheckLength =
          input.length;
        //falltrough
        return input;
    };
  
  
  
    TypingTrainer.prototype.tryRemoveBuffer =
      function (input) {
        var noTypeError = true;
        if (!input) {
          this.score.lastCheckLength =
            input.length
          return '';
        }
        while (input.length > 0 &&
          noTypeError) {
          var initSize =
            input.length
          input = tt.tryRemove(
            input);
          console.log(input);
          noTypeError = (
            initSize >
            input.length);
        }
        this.score.lastCheckLength =
          input.length
        return input;
    };
  
    TypingTrainer.prototype.init =
      function () {
        this.generateLection();
    };
  
    var tt = new TypingTrainer();
    $('#input-lifo')
      .on('keyup',
        function () {
  
          $(this)
            .val(tt.tryRemoveBuffer(
              $(this)
              .val()))
            .toggleClass(
              'hasMiss', $(
                this)
              .val()
              .length > 0)
            .attr("placeholder",
              '');
  
        }).focus();
  
    $('#lesson-select')
      .on('change',
        function () {
          tt.reloadLection($(this)
            .val());
        });
    $('#reload')
      .on('click',
        function () {
          $('#input-lifo')
            .val('')
            .focus();
          tt.clearScore();
          tt.generateLection();
        });
  
    $('body')
      .on(
        'tt.lection_update',
        function () {
  
          var i = 0,
            text = '',
            alpha = 1;
          while (i < tt.lection.length) {
            alpha = (tt.lection
              .length - i) /
              tt.lection.length;
            //workaround since chrome bug
            if (i == 0) {
              color =
                'color:orange;font-size:2.7em;';
            } else {
              color =
                'color:rgba(255,255,255,' +
                alpha +
                ');font-size:' +
                (alpha *
                alpha) * 2.5 +
                'em;'
            }
            text +=
              '<span style="' +
              color + '">';
  
            if (i == 0 && tt.lection[
              i] == " ") {
              text += '[ ]'
            } else {
              text += tt.lection[
                i]
            }
            text += '</span>';
            i++;
          }
          $('#lesson')
            .html(text);
        });
  
    $('body')
      .on('tt.score_update',
        function () {
          $('#score')
            .html(
              'Ratés | Réussis: ' +
              tt.score.misses +
              ' - ' + tt.score.hits
          );
        });
    tt.init();
  });



// Cours

var lesson = new Lesson({
    song: getSong(),
    container: document.getElementById("container"),
    result: document.getElementById("output"),
    chunks: [
    {
      type: "html",
      codeblock: [
      { c: "<div>", i: 0, n: "Créons un élément div." },
      { c: "<h1 id=\"mon-h1\">Apprendre !</h1>", i: 1, n: "Nous créons un élément h1 avec un id." },
      { c: "</div>", i: 0, n: "Nous fermons l'élément div parent. Lorsque vous faites cela, cela ajoutera votre h1 à la zone de droite." }] },
  
  
    {
      type: "css",
      codeblock: [
      { c: "h1 {", i: 0, n: "Nous pouvons sélectionner le h1 et ouvrir un nouveau bloc de déclaration CSS." },
      { c: "color: #FFBB00;", i: 1, n: "Nous pouvons maintenant rendre notre h1 de couleur jaune." },
      { c: "}", i: 0, n: "Maintenant, nous fermons le bloc de déclaration. Cela ajoutera le style dans la partie de droite." }] },
  
  
    {
      type: "css",
      codeblock: [
      { c: "#output {", i: 0, n: "Nous pouvons sélectionner le bloc de sortie à droite par son id et créer un nouveau bloc de déclaration." },
      { c: "text-align: center;", i: 1, n: "On va lui donner un alignement central." },
      { c: "background-color: #444;", i: 1, n: "Et changez la couleur de fond." },
      { c: "}", i: 0, n: "Maintenant, nous allons fermer le bloc de déclaration." }] },
  
  
    {
      type: "javascript",
      codeblock: [
      { c: "function ourFirstFN() {", i: 0, n: "Nommons une nouvelle fonction JavaScript." },
      { c: "var our_h1 = document.getElementById(\"mon-h1\");", i: 1, n: "Nous pouvons sélectionner le h1 par l'id que nous lui avons donné." },
      { c: "mon_h1.style.color = \"white\";", i: 1, n: "Maintenant, rendons notre h1 blanc au lieu de jaune." },
      { c: "}", i: 0, n: "Nous devons fermer la fonction." },
      { c: "ourFirstFN();", i: 0, n: "Maintenant, nous pouvons appeler la fonction que nous venons de créer et elle s'exécutera." }] }],
  
  
  
    completeLesson: function () {
      console.log("Leçon terminée.");
    } });
  
  /*******************************
  LESSON
  contains many chunks
  tasks are lines of a code block
  *******************************/
  
  function Lesson(params) {
    var L = {};
  
    init();
  
    function init() {
      setLesson();
      buildInstrument();
      buildChunks();
      bindKeyEvents();
      enableFirstChunk();
    }
  
    /***********
    setters
    ***********/
  
    function setLesson() {
      L.song = params.song;
      L.note = 0;
      L.container = params.container;
      L.result = params.result;
      L.chunk = 0;
      L.completeLesson = params.completeLesson;
      L.currentChunk = currentChunk;
      L.nextChunk = nextChunk;
    }
  
    /***********
    builders
    ***********/
  
    function buildChunks() {
      L.chunks = [];
      for (var c = 0; c < params.chunks.length; c++) {if (window.CP.shouldStopExecution(0)) break;
        var data = params.chunks[c];
        var chunk = new Chunk({
          type: data.type,
          result: L.result,
          codeblock_pre: data.codeblock_pre,
          codeblock: data.codeblock,
          codeblock_post: data.codeblock_post,
          completeChunk: completeChunk });
  
        L.chunks.push(chunk);
        L.container.appendChild(chunk.el);
      }window.CP.exitedLoop(0);
    }
  
    /***********
    binders
    ***********/
  
    function bindKeyEvents() {
      document.body.addEventListener("keypress", function (e) {
        var hit = L.currentChunk().currentTask().checkHit(getChar(e.which));
        if (hit) playNote();
      });
    }
  
    function getChar(which) {
      var offset = 32;
      var key_code_map = [" ", "!", "\"", "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ":", ";", "<", "=", ">", "?", "@", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "[", "\\", "]", "^", "_", "`", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "{", "|", "}", "~"];
      return key_code_map[which - offset];
    }
  
    function buildInstrument() {
      L.instrument = new Tone.PolySynth(6, Tone.SimpleAM).toMaster();
  
      L.instrument.set({
        envelope: {
          attack: 0.5,
          release: 0.5 } });
  
  
  
  
      L.instrument.set("carrier", {
        volume: 0,
        oscillator: {
          type: "triangle8",
          partials: [1, 0.2, 0.01] },
  
        envelope: {
          attack: 0.05,
          decay: 0.02,
          sustain: 0.6,
          release: 0.8 } });
  
  
      L.instrument.set("modulator", {
        volume: -8,
        oscillator: {
          type: "triangle8",
          detune: 0,
          phase: 90,
          partials: [1, 0.2, 0.01] },
  
        envelope: {
          attack: 0.05,
          decay: 0.01,
          sustain: 1,
          release: 1 } });
  
  
    }
  
    /***********
    private
    ***********/
  
    function enableFirstChunk() {
      L.currentChunk().enableChunk();
    }
  
    function currentChunk() {
      return L.chunks[L.chunk];
    }
  
    function nextChunk() {
      return L.chunk < L.chunks.length - 1 ? L.chunks[L.chunk + 1] : null;
    }
  
    function completeChunk() {
      var curr = L.currentChunk();
      if (L.chunk + 1 < L.chunks.length) {
        var next = L.nextChunk();
        if (next) {
          next.el.className = next.base_class_name + " present";
          var next_task = next.currentTask();
          next_task.el.className = next_task.base_class_name + " present";
        }
        curr.el.className = curr.base_class_name + " past";
        L.chunk++;
        L.currentChunk().enableChunk();
      } else {
        L.completeLesson();
      }
    }
  
    function playNote() {
      L.instrument.triggerAttackRelease(L.song[L.note], "8n");
      L.note = L.note + 1 < L.song.length ? L.note + 1 : 0;
    }
  
  
    return L;
  }
  
  /*******************************
  CHUNK
  contains many tasks
  tasks are lines of a code block
  *******************************/
  
  function Chunk(params) {
    var C = {};
  
    init();
  
    function init() {
      setChunk();
      buildElement();
      buildTasks();
    }
  
    /***********
    setters
    ***********/
  
    function setChunk() {
      C.result = params.result;
      C.type = params.type;
      C.task = 0;
      C.codeblock_pre = params.codeblock_pre;
      C.codeblock = params.codeblock;
      C.codeblock_post = params.codeblock_post;
      C.completeChunk = params.completeChunk;
      C.increment = increment;
      C.currentTask = currentTask;
      C.nextTask = nextTask;
      C.enableChunk = enableChunk;
    }
  
    /***********
    builders
    ***********/
  
    function buildElement() {
      C.el = document.createElement("div");
      C.base_class_name = "chunk type-" + C.type;
      C.el.className = C.base_class_name;
    }
  
    function buildTasks() {
      C.tasks = [];
      for (var c = 0; c < C.codeblock.length; c++) {if (window.CP.shouldStopExecution(1)) break;
        var line = C.codeblock[c];
        var task = new Task({
          code: line.c,
          indent: line.i,
          notes_el: params.notes_el,
          notes: line.n,
          completeTask: completeTask });
  
        C.tasks.push(task);
        C.el.appendChild(task.el);
      }window.CP.exitedLoop(1);
    }
  
    /***********
    private
    ***********/
  
    function currentTask() {
      return C.tasks[C.task];
    }
  
    function nextTask() {
      return C.task < C.tasks.length - 1 ? C.tasks[C.task + 1] : null;
    }
  
    function increment() {
      if (C.task < C.tasks.length - 1) {
        C.task++;
      } else {
        C.completeChunk();
      }
    }
  
    function completeTask() {
      var curr = C.currentTask();
      if (C.task + 1 < C.tasks.length) {
        var next = C.nextTask();
        if (next) {
          next.el.className = next.base_class_name + " present";
          next.currentNode().el.className = "node present";
        }
        curr.el.className = curr.base_class_name + " past";
        C.task++;
      } else {
        curr.el.className = curr.base_class_name + " past";
        C.completeChunk();
        runCode();
      }
    }
  
    function runCode() {
      var code = "";
  
      if (C.codeblock_pre) code += C.codeblock_pre + "\n";
      for (var i = 0; i < C.codeblock.length; i++) {if (window.CP.shouldStopExecution(2)) break;
        code += C.codeblock[i].c + "\n";
      }window.CP.exitedLoop(2);
      if (C.codeblock_post) code += "\n" + C.codeblock_post;
  
      switch (C.type) {
        case "javascript":
        case "js":
          eval(code);
          break;
        case "html":
          C.result.innerHTML += code;
          break;
        case "css":
          injectCSS(code);
          break;
        default:
        // console.debug(code);
      }
    }
  
    function enableChunk() {
      C.tasks[0].el.className += " present";
      C.tasks[0].nodes[0].el.className += " present";
    }
  
    function injectCSS(code) {
      var el = document.createElement("style");
      el.innerHTML = code;
      document.body.appendChild(el);
    }
  
    return C;
  
  }
  
  
  
  /*******************************
  TASK
  contains many nodes
  nodes are individual characters
  *******************************/
  
  function Task(params) {
    var T = {};
  
    init();
  
    function init() {
      setTask();
      buildElement();
      buildNodes();
      setEvents();
    }
  
    /***********
    setters
    ***********/
  
    function setTask() {
      T.code = params.code;
      T.indent = params.indent;
      T.notes = params.notes;
      T.base_class_name = "task indent-" + T.indent;
      T.nodes = [];
      T.node = 0;
    }
  
    function setEvents() {
      T.checkHit = checkHit;
      T.increment = increment;
      T.currentNode = currentNode;
      T.nextNode = nextNode;
      T.completeTask = params.completeTask;
    }
  
    /***********
    builders
    ***********/
  
    function buildElement() {
      var el = document.createElement("span");
      el.className = T.base_class_name;
      T.el = el;
      if (T.notes) {
        T.task_el = document.createElement("div");
        T.task_el.className = "notes";
        T.task_el.innerHTML = "<p>" + T.notes + "</p>";
        T.el.appendChild(T.task_el);
      }
    }
  
    function buildNodes() {
      var chars = T.code.split("");
      for (var c = 0; c < chars.length; c++) {if (window.CP.shouldStopExecution(3)) break;
        var char = chars[c];
        var node = document.createElement("span");
        node.className = "node";
        node.innerHTML = chars[c];
        T.nodes.push({ el: node, val: chars[c] });
        T.el.appendChild(node);
      }window.CP.exitedLoop(3);
    }
  
    /***********
    events
    ***********/
  
    function checkHit(key) {
      var curr = T.currentNode();
      if (key === curr.val) {
        var next = T.nextNode();
        if (next) next.el.className = "node present";
        curr.el.className = "node past";
        T.increment();
        return true;
      } else {
        curr.el.className = "node present miss";
        setTimeout(function () {
          if (curr.el.className !== "node past") {
            curr.el.className = "node present";
          }
        }, 200);
        return false;
      }
    }
  
    /***********
    private
    ***********/
  
    function currentNode() {
      return T.nodes[T.node];
    }
  
    function nextNode() {
      return T.node < T.nodes.length - 1 ? T.nodes[T.node + 1] : null;
    }
  
    function increment() {
      if (T.node < T.nodes.length - 1) {
        T.node++;
      } else {
        T.completeTask();
      }
    }
  
    return T;
  
  }
  
  
  function getSong() {
    var notes = ["C3", "D3", "E3", "F3", "G3", "A3", "B3", "C4", "B3", "A3", "G3", "F3", "E3", "D3"];
    return notes;
  }
  //# sourceURL=pen.js