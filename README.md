# ThemeWarlock

ThemeWarlock is a theme builder designed for quick preview and deployment of WordPress themes.

**This project is in beta, not ready for public testing**

# Addons

Addons are a way to dynamically alter the functionality of your WordPress theme.

They can be enabled and configured through the web editor.

Each addon's configuration options are stored in **Model_Project_Config_Item** 
objects.

You can customize each addon's deployment with the use of **{tags}**.

## Folder structure

Enabled addon's files are automatically copied over to the final project destination
and all custom tags are replaced with their respective values.

File names also support **data tags**.

The **go.php** file and the **go** folder will be skipped.

## The go folder

The **go** folder is optional and it has the following structure:

 * / **PresetName**
    * / **style.css**
    * / **\_style.css**
    * / **customizer.css**
    * / **functions.js**
    * / **\_functions.js**
 * / ...

The **PresetName** can be any string describing that CSS/JS addon preset (also known as a **flavor**).

The user can then select that preset from the web interface to customize the current addon's UI.

All files in the **go** folder support **data tags** only.

The contents of the **style.css** file is automatically appended to the core theme's style file.
Automatic re-indexing of the CSS comment headers takes place.

The contents of the **functions.js** file is automatically appended to the core theme's function file, as a worker function:

> {project.prefix}_instance.addWorker("__ADDON__", function(addonName, _this) {
> 
> }

The contents of the **customizer.css** file is parsed and used to populate the **cssRules** property of the **Addon** class.

If a **customizer.css** file is not declared for the current flavor, the default one will be used instead.

Each rule has the following structure:

> /\* key.subkey \*/
> 
> cssSelector {
> 
>         cssProperty: cssValue;
> 
> }

The CSS comment block is very important. Its contents will be converted into an associative array structure.

The CSS rules associated with each comment block extend up until the next comment block or until the EOF.

In the above example, the **cssRules** property in **Addon** becomes

> array('key' => array('subkey' => 'cssSelector {...}'))

The contents of the **functions.js** file is automatically appended to the **js/functions.js** file,
right before the **{project.prefix}_instance.init();** statement, encased in a 
**{project.prefix}_instance.addWorker()** method call.

The contents of the **\_functions.js** and **\_style.css** are **appended**, whereas **functions.js** and
**style.css** **replace** the corresponding files from the **default** flavor.

## The go.php file

### Naming conventions

The **go.php** file must contain a **class** that extends **Addon**.

The class name should begin with **Addon_** and be followed by a **camel-cased**,
**alpha-numeric**, **capitalized** variant of the addon's folder name.

> Example: for the addon **core-custom-colors**, the class name should be 
> **Addon_CoreCustomColors**.

### Functionality

#### Methods

1. You can execute addon-specific tasks at certain points during the deployment of
your WordPress theme by using **before** and **after** **hooks**.

    The method naming convention is as follows:

    * before / after
    * task name (from **/lib/Tasks**) without the leading number

    > Example: 
    > 
    > * **afterNewProject()**
    > * **beforeRelease()**

2. You can set this addon's user-configurable options and other WordPress-related
details by defining or extending the **Addon** class's methods:

    > List of extendable methods:
    >
    > * **getOptions()**
    > * **getPlugins()**
    > * **getTags()**
    > * **initCustomizer()** - see **Data tags / {call}**
    > * **initDrawables()** - see **Data tags / {call}**
    > * **assert($testName)** - see **Action tags**

3. You can use any method defined in your addon class by referencing it with the 
**{call}** tag.

    > Example:
    > 
    > Method **getTimeOfDay()**'s result will replace the **{call.addonName.getTimeOfDay}**
    > tag in this addon's files.

#### Properties

1. **$addonData**

    Model_Project_Config_Item[]. Array of this addon's configuration items, as 
defined in the **getOptions()** method.

2. **$addonIcon**

    String. **Twitter_Bootstrap_GlyphIcon::GLYPH_*** icon to use when listing this addon.

3. **$safeMethods**

    String[]. The results of these methods will not be escaped.


## Tags

You can use any one of the following tags to customize your addon files.

Currently, tags are supported only for the following file extensions:

  * **php** \*
  * **phtml** \*
  * **html**
  * **xhtml**
  * **js** \*
  * **css**
  * **cfg**
  * **txt**

\* These files also feature automatic **escaping** in order to prevent code 
injection by any of the designers working on the WordPress themes.

You can call any method from the current framework agnostically by refering to it
as **"framework"** in any action tag or data tag that expects an **addonName** argument.

> Example: 
> 
> * `{call.framework.methodName}`
> * `{add **if**="framework.x"}`
> * `{if.framework.testName}`
> 
> is the same as
> 
> * `{call.onepage.methodName}`
> * `{add **if**="onepage.x"}`
> * `{if.onepage.testName}`
> 
> if the current framework is "onepage"

### Action tags

Action tags **DO NOT** allow **nesting**!

They are to be treated as TODO list items. Actions are executed in the order they
are defined.

Action tags are executed **after** the data tags.

All action tags share the following property:

  * **if** - (Optional) execute the action tag only if the assertion test passes

    > {add **if**="addonName.testName"}
    > 
    >         Text to insert
    > 
    > {/add}

The **addonName** is the name of the addon that has defined the **assert($testName)** method.

Example:

        public function assert($testName) {

            switch($testName) {

                case 'x':
                    // Perform any type of validation here
                    // ...
                    return true; 

            }

        }

The **$testName** is the name of the test you wish to pass before executing this tag.

The test passes only if the **assert($testName)** method returns boolean **true** (or equivalent)!

#### {add}

Add a block of text in specific locations over an existing file using RegEx.

1. Before a block of text

    > {add **before**="regex"}
    > 
    >         Text to add before a specified location
    > 
    > {/add}

2. After a block of text

    > {add **after**="regex"}
    > 
    >         Text to add after a specified location
    > 
    > {/add}

3. Replace a block of text

    > {add **replace**="regex"}
    > 
    >         Text replacement
    > 
    > {/add}

#### {remove}

Remove a block of text in a specific location from an existing file using RegEx.

1. Remove a block of text

    > {remove}regex{/remove}

### Data tags

Data tags **ALLOW nesting**. This is especially useful when using the **{foreach}** 
or **{if}** / **{else}** tags is combination with other data tags.

Data tags are replaced with string representations.

Whether or not these strings are escaped depends on the file type and data tag.

You can force disable escaping by capitalizing the data tag.

> Example:
> 
> * `{Call.addonName.methodName}`

As opposed to action tags, data tags can also be used for **file names**.

> Example:
> 
> Local file name: **{config.ns}_icon.png**
> 
> Final file name: **st_icon.png**

#### {options}
The options tag holds all of the current project's options, including the core 
options and each individual addon's options for **cross-addon** configuration access.

Automatic escaping takes place for the following file extensions:

  - **.js**: `json_encode`
  - **.php**, **.phtml**: `var_export`

> Structure:
> 
> * `{options.[core addon option]}`
> * `{options.projectAddons.[addon name].[addon option]}`
> * `{options._staging}`: 'y' or 'n'
> * `{options._snapshotId}`: numeric, current snapshot ID

> Example: 
> 
> * `{options.projectVersion}`
> * `{options.projectAddons.core-custom-colors.color1}`

#### {addon}

The addon tag holds all of the current addon's options for ease of access.

The following are equivalent if the current addon is **core-custom-colors**:

 - `{options.projectAddons.core-custom-colors.color1}`
 - `{addon.color1}`

Automatic escaping takes place for the following file extensions:

  - **.js**: `json_encode`
  - **.php**, **.phtml**: `var_export`

> Example: 
> 
> * `{addon.projectName}`
> * `{addon.projectIcon}`

#### {if}

Insert a block of text on the spot if the validation succeeds.

> Example:
> 
> {if.addonName.testName}
> 
>         // testing??
> 
> {/if.addonName.testName}

The **addonName** is the name of the addon that has defined the **assert($testName)** method.

Example:

        public function assert($testName) {

            switch($testName) {

                case 'x':
                    // Perform any type of validation here
                    // ...
                    return true; 

            }

        }

The **$testName** is the name of the test you wish to pass before executing this tag.

The test passes only if the **assert($testName)** method returns boolean **true** (or equivalent)!

If no **testName** is provided, the assertion is validated if the addon is enabled.

#### {else}

Insert a block of text on the spot if the validation fails.

> Example:
> 
> {else.addonName.testName}
> 
>         // testing??
> 
> {/else.addonName.testName}

The **addonName** is the name of the addon that has defined the **assert($testName)** method.

Example:

        public function assert($testName) {

            switch($testName) {

                case 'x':
                    // Perform any type of validation here
                    // ...
                    return false; 

            }

        }

The **$testName** is the name of the test you wish to fail before executing this tag.

The test fails only if the **assert($testName)** method returns a value other than boolean **true** (or equivalent)!

If no **testName** is provided, the assertion is validated if the addon is disabled.

#### {foreach}

Use a block of text as a template, then iterate over an array.

The array keys and values will get automatically replaced.

Use **{@key}** for the escaped keys and **{@value}** for the escaped values.

Use **{@Key}** for the unescaped keys and **{@Value}** for the unescaped values.

> Example:
> 
> {foreach.addonName.methodName}
> 
>         // Testing {@key}
> 
>         // Testing {@value}
> 
> {/foreach.addonName.methodName}

You can also define custom key and value variables in order to handle nested
foreach statements using the **as="key.value"** attribute.

> Example:
> 
> {foreach.addonName.methodName}
> 
>         // Testing {@key}
> 
>         // Testing {@value}
> 
>         {foreach.addonName.methodNameBeta as="keyBeta.valueBeta"}
> 
>             // Testing {@keyBeta}
>
>             // Testing {@valueBeta}
> 
>         {/foreach.addonName.methodNameBeta}
> 
> {/foreach.addonName.methodName}

The **addonName** is the name of the addon that has defined the **methodName()** method.

The result of the **methodName()** must be an associative array!

You can also pass extra arguments to the method.

> Example:
> 
> {foreach.addonName.methodName.arg1.arg2}
> 
>         // Testing {@key}
> 
>         // Testing {@value}
> 
> {/foreach.addonName.methodName.arg1.arg2}

The **{@value}** tags support traversing for associative arrays, objects properties 
and objects methods - without additional arguments.

> Example:
> 
> {@value.key}
> {@value.objectProperty}
> {@value.objectMethod}
> {@value.key.objectProperty.objectMethod}

Traversing overflow is ignored; the last valid tree branch is used instead of null.

> Example:
> 
> Suppose we have this array as input: array("foo" => array("bar" => "baz"))
>
> {@key} = "foo"
> {@value} = array("bar" => "baz")
> {@value.bar} = "baz"
> {@value.bar.extra} = "baz"

#### {call}

The call tag holds the result of calls to custom methods defined in addon's **go.php** files.

Automatic escaping takes place for the following file extensions:

  - **.js**: `json_encode`
  - **.php**, **.phtml**: `var_export`

Automatic escaping can be disabled for each method individually by listing it in 
the **$safeMethods** public static property:

        public static $safeMethods = array('doNotEscapeThisMethodsResult');

> Example: 
> 
> * `{call.core-custom-colors.codePrepareCss}`
> * `{call.core-custom-colors.codeRegisterColors}`

If you have defined the **initCustomizer()** method in your addon, you can use the 
**customizer** tag to reference **WordPress_Customizer_Element_Item** objects' methods.

> Example:
> 
> * `{call.onepage.customizer._register}`
> * `{call.onepage.customizer._stylize}`
> * `{call.onepage.customizer.layout-toggle.exportVarInit}`
> * `{call.onepage.customizer.layout-toggle.exportVarName}`
> * `{call.onepage.customizer.layout-toggle.getTransport}`

You should do all image processing inside the **initDrawables()** method.

Available image processing tools:

* **$this->_image**
* **$this->_imageMagick**
* **$this->_imagick**

> Example of getting the path to a designer-set file:
> 
> `$this->addonData[self::KEY_IMAGE]->getPath();`


> Save the drawables to the project path:
> 
> `Tasks_1NewProject::getPath();`

#### {utils}

The utils tag holds the result of calls to custom methods defined in the 
**Addons_Utils** class.

Automatic escaping takes place for the following file extensions:

  - **.js**: `json_encode`
  - **.php**, **.phtml**: `var_export`

Automatic escaping can be disabled for each method individually by listing it in 
the **$safeMethods** public static property:

        public static $safeMethods = array('doNotEscapeThisMethodsResult');

> Safe methods: 
>
> * `{utils.common.copyright}`
> * `{utils.common.quote}`
> * `{utils.common.tagsList}`
> * `{utils.common.themeUrl}`
>  
> Escaped methods:
> 
> * `{utils.color.rgba.[current addon's color option]}`
> * `{utils.color.wp.[current addon's color option]}`

#### {project}

The project tag contains project structure-specific data.

The keys represent the public static properties of **Tasks_1NewProject**.

Automatic escaping **DOES NOT** take place. All values are used as such 
regardless of file extension.

> Possible values: 
> 
> * `{project.sourceDir}`
> * `{project.destDir}`
> * `{project.prefix}`
> * `{project.destAuthorName}`
> * `{project.destProjectName}`
> * `{project.versionVerbose}`

#### {framework}

The framework tag contains framework-specific data.

This information is defined in the corresponding **info.php** file for this 
framework.

Automatic escaping **DOES NOT** take place. All values are used as such 
regardless of file extension.

> Possible values: 
> 
> * `{framework.framework_target}`
> * `{framework.framework_id}`

#### {config}

The config tag contains the **config.ini** values.

Automatic escaping **DOES NOT** take place. All values are used as such 
regardless of file extension.

> Possible values: 
> 
> * `{config.authorName}`
> * `{config.authorUrl}`
> * `{config.authorEmail}`
> * `{config.appMode}`
> * `{config.logLevel}`
> * `{config.getUse}`
> * ...