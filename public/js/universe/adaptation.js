 // Game of life

    const size = 15;
    const spawnRange = 5;
    const spawnProbability = 0.25;
    const colors = ['#4285f4', '#34a853', '#fbbc05', '#ea4335'];
    let colorIdx = 0;
    let shouldKill = false;
    let animatePromise = null;
    let clickListener = null;
    
    const canvas = document.querySelector('#canvas1');
    const ctx = canvas.getContext('2d');
    let startedAnimating = false;
    let spawn = null;
    
    // ======== Util Functions ========
    
    const setHandleResize = (func, ms = 100) => {
      let id;
      window.onresize = () => {
        clearTimeout(id);
        id = setTimeout(func, ms);
      };
    };
    
    const animate = (func, shouldStop, fps) => {
      const fpsInterval = 1000 / fps;
      let then = Date.now();
      const loop = (resolve) => {
        if (shouldStop()) {
          resolve();
          return;
        }
    
        requestAnimationFrame(() => loop(resolve));
        // Enforce fps
        const now = Date.now();
        const elapsed = now - then;
        if (elapsed <= fpsInterval) return;
        then = now - (elapsed % fpsInterval);
    
        func();
      };
      return new Promise(resolve => loop(resolve));
    };
    
    const mod = (x, n) => (x % n + n) % n;
    
    const make2dArray = (w, h, v) => {
      const map = new Array(h);
      for (let y = 0; y < h; y++) {
        map[y] = new Array(w);
        for (let x = 0; x < w; x++) {
          map[y][x] = (typeof v === 'function') ? v() : v;
        }
      }
      return map;
    };
    
    const dimensions = (map) => [map[0].length, map.length];
    
    // ======== GoL Functions ========
    
    const isLive = (map, x, y) => {
      const [w, h] = dimensions(map);
      return (
        x >= 0 && x < w &&
        y >= 0 && y < h &&
        Math.abs(map[y][x][0]) === 1
      );
    };
    
    const moves = [[0, 1], [1, 0], [1, 1], [0, -1],
                   [-1, 0], [-1, -1], [-1, 1], [1, -1]];
    
    const getLiveNeighbors = (map, x, y) => {
      const [w, h] = dimensions(map);
      const neighbors = moves.map((move) => {
        const i = mod(x + move[0], w);
        const j = mod(y + move[1], h);
        return [i, j];
      });
      return neighbors.filter(([x, y]) => isLive(map, x, y));
    };
    
    const step = (map) => {
      const [w, h] = dimensions(map);
      for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
          const liveNeighbors = getLiveNeighbors(map, x, y);
          const n = liveNeighbors.length;
          if (map[y][x][0] === 1 && (n < 2 || n > 3)) {
            map[y][x][0] = -1; // Will die
          }
          if (map[y][x][0] === 0 && n === 3) {
            map[y][x][0] = 2; // Will live
            // Inherit color from a random neighbor
            const [nx, ny] = liveNeighbors[Math.random() * n | 0];
            map[y][x][1] = map[ny][nx][1];
          }
        }
      }
      for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
          if (map[y][x][0] === -1) { map[y][x][0] = 0; }
          if (map[y][x][0] === 2) { map[y][x][0] = 1; }
        }
      }
      if (spawn != null) {
        const [spawnX, spawnY] = spawn;
        spawn = null;
        for (let y = spawnY - spawnRange; y < spawnY + spawnRange; y++) {
          for (let x = spawnX - spawnRange; x < spawnX + spawnRange; x++) {
            // Only spawn additively
            if (Math.random() < spawnProbability) {
              const i = mod(x, w);
              const j = mod(y, h);
              map[j][i][0] = 1;
              map[j][i][1] = colorIdx;
            }
          }
        }
        colorIdx = (colorIdx + 1) % colors.length;
      }
    };
    
    // ======== Main Functions ========
    
    const render = (map) => {
      const [w, h] = dimensions(map);
      const unusedX = canvas.width - (w * size);
      const unusedY = canvas.height - (h * size);
    
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
          if (map[y][x][0] === 1) {
            ctx.fillStyle = colors[map[y][x][1]];
            ctx.fillRect(unusedX / 2 + x * size, unusedY / 2 + y * size, size, size);
          }
        }
      }
      step(map);
    };
    
    const handleClick = (e, map) => {
      if (!startedAnimating) {
        startedAnimating = true;
        animatePromise = animate(() => render(map), () => shouldKill, /* fps */ 10);
      }
      const [w, h] = dimensions(map);
      const spawnX = e.clientX / window.innerWidth * w | 0;
      const spawnY = e.clientY / window.innerHeight * h | 0;
      spawn = [spawnX, spawnY];
    };
    
    const setUp = async () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      const w = canvas.width / size | 0;
      const h = canvas.height / size | 0;
      const map = make2dArray(w, h, () => [0, /* color */ 0]);
    
      ctx.font = '2em "Ubuntu Mono", monospace';
      ctx.textAlign = 'center';
      ctx.fillStyle = 'white';
      ctx.fillText('Cliques pour commencer', canvas.width / 2, canvas.height / 2);
      
      if (animatePromise != null) {
        shouldKill = true;
        await animatePromise;
        shouldKill = false;
      }
    
      if (clickListener != null) {
        canvas.removeEventListener('click', clickListener, false);
        startedAnimating = false;
        spawn = null;
      }
      clickListener = (e) => handleClick(e, map);
      canvas.addEventListener('click', clickListener, false);
    };
    
    window.onload = () => {
      setHandleResize(setUp);
      setUp();
    };





// Biomorphose

class Biomorph
   {
   constructor()
     {
     this.genome=[];
     }
   randomGenome()
     {//Fills genome with random commands
     var length=32+Math.random()*32;
     this.genome=[];
     for(var ind=0;ind<length;ind++)
       {
       this.genome.push([Math.floor(6*Math.random()),Math.floor(25*Math.random()-12)])
       }
     }
   reproduce()
     {
     var offspring=new Biomorph();
     var loc=Math.floor(this.genome.length*Math.random());
     for(var ind=0;ind<this.genome.length;ind++)
       {
       var mutationType=0;
       if(ind==loc){mutationType=Math.floor(1+6*Math.random())}
       switch(mutationType)
         {
         case 1://Value point mutation
             offspring.genome.push([this.genome[ind][0],
                                    this.genome[ind][1]+Math.floor(3*Math.random()-1)]);
         break;
         case 2://Type point mutation
             offspring.genome.push([Math.floor(6*Math.random()),
                                    this.genome[ind][1]])
         break;
         case 3://Copy gene
             offspring.genome.push(this.genome[ind]);
             offspring.genome.push(this.genome[ind]);
         break;
         case 4://Random insertion
             offspring.genome.push(this.genome[ind]);
             offspring.genome.push([Math.floor(6*Math.random()),Math.floor(25*Math.random()-12)])
         break;
         case 5:case 6://Delete gene
         break;
         default://Otherwise...
             offspring.genome.push(this.genome[ind]);
         break;
         }
       }
     return offspring;
     }
   draw(x,y,scale,angle,ctx)
     {
     var strokes=[{x:0,//Untransformed x...
                   y:0,//y...
                   a:0,//and angle
                   f:false,//flip
                   i:0,//Index in genome
                   p:[]//indexes of checkpoints,so it doesn't do too many times
                  },{x:0,y:0,a:0,f:true,i:0,p:[]
                  }];
     var paths=[];
     var bound=0;
     while(strokes.length>0)
       {
       paths.unshift([[strokes[0].x,strokes[0].y]]);
       while(strokes[0].i<this.genome.length)
         {
         if (!strokes[0].p.includes(strokes[0].i))
           {
           var gene=this.genome[strokes[0].i];
           switch(gene[0])
             {
             case 0://Spawn new stroke
                var newStroke={x:strokes[0].x,
                                y:strokes[0].y,
                                a:strokes[0].a,
                                f:strokes[0].f,
                                i:Math.max(0,strokes[0].i+Math.abs(gene[1])+1)};
                 newStroke.p=strokes[0].p.concat(strokes[0].i);
                 strokes.push(newStroke);
             break;
             case 1://Jump position in genome
                strokes[0].p.push(strokes[0].i);
                strokes[0].i=Math.max(0,strokes[0].i-Math.floor(gene[1]/2));
             break;
             case 2://Rotate
                strokes[0].a+=gene[1]*(2*strokes[0].f-1);
             break;
             case 3://Flip
                strokes[0].f=!strokes[0].f;
             break;
             case 4://Moves stroke
                 strokes[0].x+=Math.cos(Math.PI*strokes[0].a/12);
                 strokes[0].y+=Math.sin(Math.PI*strokes[0].a/12);
                 paths[0].push([strokes[0].x,strokes[0].y]);
                 bound=Math.max(bound,Math.abs(strokes[0].x),Math.abs(strokes[0].y))
             break;
                  //5 is just 'junk' DNA that can be turned into something useful
             }
           }
         strokes[0].i++;
         }
      // ctx.stroke();
       strokes.shift();
       }
     var scale2=scale/bound;
     for(var ind1=0;ind1<paths.length;ind1++)
       {
       ctx.beginPath();
       ctx.lineWidth=2;
       ctx.moveTo(x+scale2*(Math.cos(angle)*paths[ind1][0][0]-Math.sin(angle)*paths[ind1][0][1]),
                  y+scale2*(Math.sin(angle)*paths[ind1][0][0]+Math.cos(angle)*paths[ind1][0][1]));
       for(var ind2=1;ind2<paths[ind1].length;ind2++)
         {
         ctx.lineTo(x+scale2*(Math.cos(angle)*paths[ind1][ind2][0]-Math.sin(angle)*paths[ind1][ind2][1]),
                    y+scale2*(Math.sin(angle)*paths[ind1][ind2][0]+Math.cos(angle)*paths[ind1][ind2][1]));
         }
       ctx.stroke();
       }
     }
   }
var canvases=[];
for(var xx=0;xx<2;xx++)
  {
  for(var yy=0;yy<8;yy++)
    {
    var canv=document.createElement('canvas');canvases.push(canv);
    canv.classList.add('canvas2');
    canv.width=150;canv.height=150;
    document.getElementById('biomorphs').appendChild(canv);
    canv.ctx=canv.getContext('2d');
    canv.morph=new Biomorph();
    canv.morph.randomGenome();
    canv.morph.draw(75,75,50,Math.PI/2,canv.ctx);
    canv.onclick=function(){
      for(var ind=0;ind<canvases.length;ind++)
        {
        if(canvases[ind]!=this)
          {
          canvases[ind].morph=this.morph.reproduce();
          canvases[ind].ctx.clearRect(0,0,150,150);
          canvases[ind].morph.draw(75,75,50,Math.PI/2,canvases[ind].ctx);
          }
        }
      document.getElementById('genome').value=canv.morph.genome;
      };
    }
  document.getElementById('biomorphs').appendChild(document.createElement('br'));
  }
document.getElementById('load').onclick=function(){
  var str=document.getElementById('genome').value+",";
  var newGenome=[];
  var pair=[];
  var numstr="";
  for(var char=0;char<str.length;char++)
    {
    if(str[char]==",")
      {
      pair.push(Number(numstr));numstr="";
      if(pair.length>1){newGenome.push(pair);pair=[];}
      }
    else
      {
      numstr+=str[char];
      }
    }
  canvases[0].morph.genome=newGenome;
  canvases[0].ctx.clearRect(0,0,150,150);
  canvases[0].morph.draw(75,75,50,Math.PI/2,canvases[0].ctx);
  for(var ind=1;ind<canvases.length;ind++)
    {
    canvases[ind].morph=canvases[0].morph.reproduce();
    canvases[ind].ctx.clearRect(0,0,150,150);
    canvases[ind].morph.draw(75,75,50,Math.PI/2,canvases[ind].ctx);
    }
}


//Adaptation Section

$(document).ready(function(){

  // Article

  $(".blog-post-1").click(function(){
    if ($("#blog-wrapper").hasClass("showblog1")) {         
        $("#blog-wrapper").removeClass("showblog1");
        setTimeout(function(){
          $(".blog-post-1").removeClass("z");
          $(".contain-blog1").removeClass("x");
      }, 500);
      } else {
        $("#blog-wrapper").addClass("showblog1");
        setTimeout(function(){
          $(".blog-post-1").addClass("z");
          $(".contain-blog1").addClass("x");
      }, 0);
      }
  })

  $(".blog-post-2").click(function(){
    if ($("#blog-wrapper").hasClass("showblog2")) {         
        $("#blog-wrapper").removeClass("showblog2");
        setTimeout(function(){
          $(".blog-post-2").removeClass("z");
          $(".contain-blog2").removeClass("x");
      }, 500);
      } else {
        $("#blog-wrapper").addClass("showblog2");
        setTimeout(function(){
          $(".blog-post-2").addClass("z");
          $(".contain-blog2").addClass("x");
      }, 0);
      }
  })

  $(".blog-post-3").click(function(){
    if ($("#blog-wrapper").hasClass("showblog3")) {         
        $("#blog-wrapper").removeClass("showblog3");
        setTimeout(function(){
          $(".blog-post-3").removeClass("z");
          $(".contain-blog3").removeClass("x");
      }, 500);
      } else {
        $("#blog-wrapper").addClass("showblog3");
        setTimeout(function(){
          $(".blog-post-3").addClass("z");
          $(".contain-blog3").addClass("x");
      }, 0);
      }
  })

});



