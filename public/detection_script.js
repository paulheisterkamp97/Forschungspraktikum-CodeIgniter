let img = null;
let width,height;
let bbList;
let canvas


//p5 Standart Funitons -------------------------------------------------------------------
function preload() {
	console.log("starting..")
}

function setup(){
	//init canvas
	element = document.getElementById("canvas");		
	canvas = createCanvas(element.clientWidth -1, element.clientWidth);
	canvas.parent('canvas');
	windowResized();

	//initevent listeners
	document.getElementById("addBox").addEventListener("click", addBoxListener);
	document.getElementById("createInvoice").addEventListener("click", collectData);

	bbList = new BBList();

	const pictureSelectors = document.getElementById('picselector').childNodes;

	for (let i = 0; i < pictureSelectors.length; i++) {
		pictureSelectors[i].addEventListener('click',selectPictureListener);
	}

}

function draw(){
	if (img != null) {
		image(img, 0, 0, width, height);
		bbList.resizeBox();
		bbList.drawBoxes();
		strokeWeight(1);
		noFill();
		stroke(0);
		rect(0, 0, width, height);
	}


	
}
function windowResized() {
	if(img != null) {
		element = document.getElementById("canvas");
		width = element.offsetWidth;
		height = width * (float) (img.height / img.width);
		resizeCanvas(width, height);
	}
}



//Standard Button Listeners -----------------------------------------------------

function selectPictureListener(){
	//delete all old inputs
	bbList = new BBList();
	document.querySelector('#partlist').innerHTML = "";

	//get id and set selected active
	picid = this.getAttribute('picid');

	const pictureSelectors = document.getElementById('picselector').querySelectorAll("button");
	for (let i = 0; i < pictureSelectors.length; i++)
	{
		pictureSelectors[i].setAttribute('class','list-group-item');
	}
	this.setAttribute('class', 'list-group-item active');


	//load resources
	img = loadImage('/get-image/'+picid,windowResized);

	$.get("/get-detection/"+picid,function (data,status) {
		if (status == "success"){
			for (let i = 0; i < data.length; i++) {
				e = data[i];
				bbList.newBox(e['x'],e['y'],e['h'],e['w'],e['class'])

			}
		}

	},'json');
}

function collectData(){
	var data  = JSON.parse(JSON.stringify(bbList.bboxes));
	for (let i = 0; i < data.length; i++) {
		data[i].h = data[i].heigth;
		delete data[i].heigth;
		data[i].w = data[i].width;
		delete data[i].width;
		data[i].class = data[i].classNr;
		delete data[i].classNr;
		delete data[i].color;
		delete data[i].id;
	}
	console.log(data);

}


function mousePressed(){
	bbList.selectBoxes();

}

function mouseReleased(){
	bbList.selectedBox = null;
}


function addBoxListener(){
	bbList.newBox(0.1,0.1,0.1,0.1,0);
}


//Data Object for Bounding boxes and Handling ------------------------------------
class BBList{
	static nameSize = 18;
	static classes = ["Muendungsabschluss","Regenhaube","Dachdurchfuehrung_gerade","Dachdurchfuehrung_geneigt","Wandhalterung","Wanddurchfuehrung","Reinigungsoeffnung","Bodenmontage","Abschluss_Konsole"];

	constructor(){
		this.bboxes = []
		this.selectedBox = null;
		this.selectedAction = "pos";
	}

	drawBoxes(){
		for (var i = 0; i < this.bboxes.length; i++) {
			this.bboxes[i].draw();
		}
	}

	newBox(x,y,h,w,cl){
		let bb = new BoundingBox(x,y,h,w,cl)
		this.bboxes.push(bb);

		var t = document.querySelector('#hitboxItem');
		var selector = t.content.querySelector("select");		
		t.content.querySelector("li").id = "hitBox_"+bb.id;

		//add Options
		var options = "";
		for (var i = BBList.classes.length - 1; i >= 0; i--) {
			options += "<option value="+i+">"+BBList.classes[i]+"</option>";
		}

		//set Option + Color
		selector.innerHTML = options;
		selector.style.borderColor  = bb.color.toString('#rrggbb');
		
		//clone an add to document
		var clone = document.importNode(t.content, true);
		document.querySelector("#partlist").appendChild(clone);

		//add listeners
		var item = document.querySelector('#hitBox_'+bb.id);
		item.querySelector('[value="'+cl+'"]').setAttribute('selected',"selected")
		item.querySelector("select").addEventListener("change", this.changeClassListener);
	 	item.querySelector("button").addEventListener("click", this.removeBoxListener);
	 }

	 changeClassListener(){
	 	var id = this.parentNode.parentNode.parentNode.id.split("_")[1]
	 	var bb = bbList.bboxes[bbList.getIndexByID(id)];
	 	bb.changeClass(this.value);
	 }

	 removeBoxListener(){
	 	var id = this.parentNode.parentNode.parentNode.id.split("_")[1]
	 	var bb = bbList.getIndexByID(id);
	 	bbList.bboxes.splice(bb,1);
	 	this.parentNode.parentNode.parentNode.remove();
	 }

	 getIndexByID(id){
	 	for (var i = this.bboxes.length - 1; i >= 0; i--) {
	 		if(this.bboxes[i].id == id){
	 			return i;
	 		}

	 	}
	 	return null;
	 }

	 selectBoxes(){
	 	for (var i = this.bboxes.length - 1; i >= 0; i--) {
	 		if(this.bboxes[i].isOnName()){
	 			this.selectedBox = this.bboxes[i];
	 			this.selectedAction = "pos";
	 			break;
	 		}else if(this.bboxes[i].isOnSize()){
	 			this.selectedBox = this.bboxes[i];
	 			this.selectedAction = "size";
	 			break;
	 		}
	 	}
	 }

	 resizeBox(){
	 	if (mouseIsPressed && this.selectedBox != null){
	 		if(this.selectedAction == "pos"){
	 			this.selectedBox.changePosition(mouseX,mouseY);
	 		}else{
	 			this.selectedBox.changeSize(mouseX,mouseY);
	 		}

	 	}
	 }

	}



	class BoundingBox{

		static idCounter = 0;

		constructor(x,y,height,width,classNr){	
			this.id = BoundingBox.idCounter++;
			this.classNr = classNr;
			this.color = color(random(255),random(255),random(255));
			this.x = x;
			this.y = y; 
			this.heigth = height;
			this.width = width;
		}

		draw(){
		//compute values needed		
		let ax = width*this.x;
		let ay = height*this.y;
		let awidth = width*this.width;
		let aheight = height*this.heigth;

		noFill();
		strokeWeight(2);
		stroke(this.color);
		rect(ax,ay,awidth,aheight);

		fill(this.color);
		rect(ax,ay-BBList.nameSize ,awidth, BBList.nameSize);

		fill(0);
		textSize(16);
		textAlign(LEFT,CENTER);
		text(BBList.classes[this.classNr],ax,ay - BBList.nameSize/2)
	}


	//change x Functions
	changePosition(x,y){
		this.y = constrain(y / height,0,1);
		this.x = constrain(x / width,0,1);
	}

	changeSize(w,h){
		this.heigth = constrain(h / height-this.y,0,1);
		this.width = constrain(w / width-this.x,0,1);
	}

	changeClass(id){		
		this.classNr = id		
	}


	//Mouse Hit box calculation
	isOnName(){
		//compute values needed	
		let ax = width*this.x;
		let ay = height*this.y;
		let awidth = width*this.x+width*this.width;
		let aheight = height*this.y+height*this.heigth;

		return (ax<mouseX && mouseX<ax+awidth && ay-BBList.nameSize<mouseY && mouseY<ay);
	}

	isOnSize(){
		//compute values needed	
		let offset = 10;
		let awidth = width*this.x+width*this.width;
		let aheight = height*this.y+height*this.heigth;

		return (awidth-offset<mouseX && mouseX<awidth+offset && aheight-offset<mouseY && mouseY<aheight+offset);

	}

}