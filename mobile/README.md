-------------- Customizing .CSS, .JS Theme Files --------------


Components Library : https://mdbootstrap.com/docs/jquery/layout/overview/


-------------- Preparing the environment --------------


Step 1 - Node.js installation
- Go to nodejs.org and download node.js
- Launch the installation program and install Node with all default settings (that means - click "next" until it gets installed).

Step 2 - Git BASH installation
- Go to git-scm.com , download the latest version.
- Launch the installation program and install Git BASH with all default settings

Step 3 - Installing Gulp globally
- ON GUEST NETWORK
	Open the Git BASH terminal
	Type:	npm install gulp@3.9.1 -g	...and click Enter.
- ON DELL CORP NETWORK
	Open Powershell with Admin Mode
	Type:	npmp install gulp@3.9.1 -g	...and click Enter.
	ENTER YOUR NT PASSWORD TO CONTINUE

Step 4 - CLONE this github repo and navigate to its parent folder
- Right Click and select GIT BASH HERE option from file menu
- TYPE:	npm init	...and click Enter 
- ALTERNATIVELY TRY:	npmp init	...and click Enter
- Then you can fill in the basic data about your project, like name, description and version. You can just leave it blank and click enter until you are asked "Is it ok? (yes)". Then confirm the question by clicking Enter.

STEP 5 - Installing Gulp Locally
- Open Powershell with admin mode
- Navigate to project's parent dir
- Type:	npmp install --save-dev gulp	...and click Enter
- Type:	npmp install --save-dev gulp-sass gulp-autoprefixer gulp-cssmin browser-sync gulp-concat gulp-minify gulp-rename gulp-imagemin		...and hit Enter

STEP 6 - launching the project and live server test
- Type gulp mysales-go command into the Git BASH terminal of project directory and click Enter


-------------- Customizing .CSS, .JS Theme Files --------------


Step 1 - launching the project
- Open the project in your code editor.
- Type gulp mysales-go command to your terminal to launch gulp.

Step 2 - compiling Sass files
- Open \scss\mdb.scss file in your code editor.
- This file contains the list of all the Sass components in the Theme package. The compilation is super easy - just remove or comment the component you don't need and save the file.
- For example - let's say you don't need all the 74 animations available in Theme, maybe because basic animations are enough for you. 
  Remove the line @import "free/animations-extended"; from the list in the mdb.scss file and save it. 
  After saving the file Theme Gulp package then automatically compiles new mdb.css and mdb.min.css files. 

  If you open these files (within the \dist\css\ directory) you will notice they don't contain extended animations. 
  Thanks to that your package takes up less storage space.

Step 3 - Sass dependencies [ REFER sass-dependencies.md file ]
- Some of the components require other components to work properly. That's why we have created a map of the dependencies.
- Please note:
	All the components require core files. If you decide to remove any of the core files you risk a conflict.
	If you remove any of the Free or Pro components - be sure it's not a dependency of any other component.
	If you remove the dependency of any the Free or Pro components - please be aware that's possible it may not work 100% properly.

Step 4 - compiling JavaScript files [ REFER js-dependencies.md file ]
- Compiling JavaScript files is very similar to the compilation of Sass files.
- Open \js\modules.js file in your code editor.
- This file contains the list of all the JavaScript components of the Theme package.
  You now do the same as with Sass files - just remove or comment the components you don't need and save the file.
  After saving the file, the Theme gulp package then compiles new mdb.js and mdb.js.css files.
  If you open these files (within the \dist\js\ directory) you will notice they don't contain any of the removed components.
