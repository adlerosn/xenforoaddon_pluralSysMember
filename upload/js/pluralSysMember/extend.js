// Modified from https://github.com/cclaerhout/xen_RedactorDemoButton

//If you want to create a plugin (see at the bottom arround the line 50), you will have to add the below line

if(typeof RedactorPlugins == 'undefined') var RedactorPlugins = {};

!function($, window, document, undefined)
{
	if(!enablepluralsysPlugin) return;

	$(document).on('EditorInit', function(e, data){
		console.info('The XenForo EditorInit event is called');
	
		var editorFramework = data.editor,	//You can access the XenForo editor framework functions here
			config = data.config; 		//You can modify the XenForo editor config here

		var pluralsysButtonCallback = function(ed, ev)
		{
      			/*If you want to insert immediately some html, uncomment the below line*/
      				//ed.execCommand('inserthtml', 'Your Html'); return false;

      			/*If you want to open an overlay*/
      			ed.saveSelection();

			//The getText function allows the text to be translated provided you reference it in the "editor_js_setup" template inside the "RELANG.xf" object
			var modalTitle = editorFramework.getText('pluralsys_title');
			
			//Ignore this
			if(typeof ev != undefined && $(ev.currentTarget).hasClass('redactor_btn_my_extra_btn_id')){
				modalTitle += ' - from the programmatically button';
			}

			//Open the modal
      			ed.modalInit(modalTitle, { url: editorFramework.dialogUrl + '&dialog=pluralsys' }, 600, $.proxy(function()
      			{
      				// Note that above the dialog name is called "pluralsys", so the targeted template will be "editor_dialog_pluralsys"
      				$('#redactor_insert_pluralsys_btn').click(function(e) {
      					e.preventDefault();
      					
      					selectionObject = ed.getSelectedHtml();
      					selected = selectionObject.toString();
      					
      					var output = $('#redactor_pluralsys_text').val();
      					output = XenForo.htmlspecialchars(output);
      						//XenForo doesn't have a full escapeHtml function, so let's tweak it a little
      						output = output
      							.replace(/\t/g, '	')
      							.replace(/ /g, '&nbsp;')
      							.replace(/\n/g, '</p>\n<p>');
      		
      					ed.restoreSelection();
      					//console.log(ed);
      					ed.execCommand('inserthtml', '[PLURALSYS="'+output+'"]'+selected+'[/PLURALSYS]');
      					//editorFramework.wrapSelectionInHtml(ed, '[pluralsys]', '[/pluralsys]', true);
      					ed.modalClose();
      				});
      				setInterval(function(){$("#redactor_pluralsys_text").focus();},200);
      			}, ed));
		};

		/*Let's tweak the pluralsys Bb Code button by registering the above callback*/
		if(typeof config.buttonsCustom != undefined && typeof config.buttonsCustom.custom_pluralsys != undefined){
			var pluralsysButton = config.buttonsCustom.custom_pluralsys;

			pluralsysButton.callback = pluralsysButtonCallback;
		}

		/***
		* For those who need to write a plugin, here's the optional code
		* If you don't need, just delete this part.
		* The plugin id here is "myPlugin"
		**/	
		RedactorPlugins.myPlugin = {
			init: function()
			{
				if(!enablepluralsysPlugin) return false;
				
				console.info('The Redactor Plugin init event is called');
				
				/**
				* If you want to listen an event
				***/
				this.$editor.on('keyup', $.proxy(function(e)
				{
					//console.info('The Key up event has been called in the editor');
					var html = this.$editor.html();
					//console.log(html);
				}, this));

				/**
				* If you want to add programmatically a new extra button
				* Disclaimer: the Redactor adding button function doesn't update some elements in its objets,
				*             so it might be difficult for other developers to check if your button has been added or not
				***/
				var extraBtnId = 'pluralsys';
				
				this.addBtn(
					extraBtnId,				//id
					'Plural System',		//title
					this.extraBtnCallback					//callback when click
					// a fourth parameter can be used to build a dropdown (check the redactor source for this)
				);
				
				//Let's modify the added button to match the default html structure 
				this.extraBtnFormatLayout(extraBtnId);
			},
			extraBtnCallback: function(ed, ev)
			{
				//console.log('The extra button has been called');
				
				//Let's plug this callback on the above callback "pluralsysButtonCallback"
				pluralsysButtonCallback(ed, ev);
			},
			extraBtnFormatLayout: function(id)
			{
				var $toolbar = this.$toolbar,
					$extraBtn = $toolbar.find('.redactor_btn_' + id),
					$wrapper = $('<li class="redactor_btn_container_' + id + '">');

				$extraBtn
					// the button will be added to the last buttons group
					.appendTo($toolbar.find('> .redactor_btn_group:last > ul')) 
					// the button needs a wrapper to match the default html structure
					.wrap($wrapper);
					
				/**
				* You just need now to set a css for this button in your extra template for example. Ie:
				*
				*	html .redactor_toolbar li a.redactor_btn_my_extra_btn_id {
				*		background-position: 3px -2845px;
				*	}
				***/
			}
		};
	
		if(typeof config.plugins === undefined || !$.isArray(config.plugins)){
			config.plugins = [];
		}
		
		config.plugins.push('myPlugin');
	});
}
(jQuery, this, document, 'undefined');
