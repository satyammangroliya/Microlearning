This Project forks the [SrTile](https://github.com/studer-raimann/SrTile.git)  by studer + raimann ag

## Installation 

Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
git clone https://github.com/Minervis-GmbH/Microlearning.git ToGo 
```
Update, activate and config the plugin in the ILIAS Plugin Administration

## Configuration  

3 Objects have to be specified here:  

- Homepage object
- Survey Object
- "Was Sind Lern-Snacks?" Object

You can read the REF_ID of each object in the raw link of the corresponding Object. The Link https://ilias.bgn-akademie.de/goto.php?target=cat_5338&client_id=bgnakademie for example is pointing to the object with REF_ID 5338.  


## USE

### create a snack (Tiles)
1. Follow the standard official way of creating objects(New Object>choose ILIAS Object>...)
2. To configure this particular object, click on the object and then click Tab 'ToGo'
3. Add Branch and Topic. 
	-  A Snack can have many branches and but only one Topic. 
	- To assign multiple branches to a snack use commas. e.g.:Topic1,Topic2,Topic3
	- If more than one Topic is assigned to a snack, only the first one will be saved
4.  Upload a snack image
5. By default the snacks are displayed as normal ILIAS Objects. To view the contents of the homepage as tiles, choose tile.
6. choose devices

### delete snacks
click on the subtab item "manage", choose the snack to delete and the delete.

### Info
The plugin implements three main functionalities: 
- Views count: records how many ILIAS registered Users have accessed a Snack
- Likes: enables like and unlike 
- The Header and Menu design

### compatibility
ILIAS 5.4