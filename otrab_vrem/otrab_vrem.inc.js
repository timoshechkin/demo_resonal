
function WinOtrabVrem(idWin)
{
	
	
	
	var monthName = Array();
	monthName[0] = "январь";
	monthName[1] = "февраль";
	monthName[2] = "март";
	monthName[3] = "апрель";
	monthName[4] = "май";
	monthName[5] = "июнь";
	monthName[6] = "июль";
	monthName[7] = "август";
	monthName[8] = "сентябрь";
	monthName[9] = "октябрь";
	monthName[10] = "ноябрь";
	monthName[11] = "декабрь";
	
	var char_code = new Array();
	char_code[81] = "Й";
	char_code[87] = "Ц";
	char_code[69] = "У";
	char_code[82] = "К";
	char_code[84] = "Е";
	char_code[89] = "Н";
	char_code[85] = "Г";
	char_code[73] = "Ш";
	char_code[79] = "Щ";
	char_code[80] = "З";
	char_code[219] = "Х";
	char_code[221] = "Ъ";
	char_code[65] = "Ф";
	char_code[83] = "Ы";
	char_code[68] = "В";
	char_code[70] = "А";
	char_code[71] = "П";
	char_code[72] = "Р";
	char_code[74] = "О";
	char_code[75] = "Л";
	char_code[76] = "Д";
	char_code[186] = "Ж";
	char_code[222] = "Э";
	char_code[90] = "Я";
	char_code[88] = "Ч";
	char_code[67] = "С";
	char_code[86] = "М";
	char_code[66] = "И";
	char_code[78] = "Т";
	char_code[77] = "Ь";
	char_code[188] = "Б";
	char_code[190] = "Ю";
	
	
	//СОЗДАЕМ ОКНО
	dhxWinOtrab = dhxWins.createWindow(idWin, 1, 1, 800, 500);//ПОЗИЦИЯ И РАЗМЕРЫ ОКНА
	var text = menu.getItemText(idWin);//ЗАГОЛОВОК ОКНА
	dhxWinOtrab.setText(text);
	dhxWinOtrab.maximize(true);

	dhxLayoutOtrab = new dhtmlXLayoutObject(dhxWinOtrab, "3L");
	dhxLayoutOtrab.cells("a").setText("Подразделения");
	//dhxLayoutOtrab.cells("a").setEffect('highlight', true);
	dhxLayoutOtrab.cells("b").setText("Фактически отработанное время за месяц по сотрудникам");
	//dhxLayoutOtrab.setEffect('resize', true);
	
	var statusbarOtrab_b = dhxLayoutOtrab.cells("b").attachStatusBar();
    statusbarOtrab_b.setText("");
	
	var dhxLayoutOtrab_c = dhxLayoutOtrab.cells("c").attachLayout("2U");
	
	
	dhxLayoutOtrab_c.cells("a").setText("Использование рабочего времени в течении дня по сотруднику");
	dhxLayoutOtrab_c.cells("b").setText("Посещения в течении дня по сотруднику");
	
	
	var loader_layout_size = dhtmlxAjax.getSync("windows/otrab_vrem/getInfo.php?action=layout_size");
	var ArrayRes_layout_size = new Array();
	ArrayRes_layout_size = loader_layout_size.xmlDoc.responseText.split('|');
	
	dhxLayoutOtrab.cells("a").setWidth(ArrayRes_layout_size[1]);
	dhxLayoutOtrab.cells("c").setHeight(ArrayRes_layout_size[2]);
	dhxLayoutOtrab_c.cells("b").setWidth(ArrayRes_layout_size[3]);
	
	dhxLayoutOtrab.attachEvent("onPanelResizeFinish",
			function()
				{
					dhtmlxAjax.post("windows/user_settings/save.php","action=update_layout_size_otrab_1&OTRAB_A_W="+dhxLayoutOtrab.cells("a").getWidth()+"&OTRAB_C_H="+dhxLayoutOtrab.cells("c").getHeight(),function(loader){});
				});
				
	dhxLayoutOtrab_c.attachEvent("onPanelResizeFinish",
			function()
				{
					dhtmlxAjax.post("windows/user_settings/save.php","action=update_layout_size_otrab_2&OTRAB_CB_W="+dhxLayoutOtrab_c.cells("b").getWidth(),function(loader){});
				});

	statusBarOtrabVrem_c_a = dhxLayoutOtrab_c.cells("a").attachStatusBar();
	
	dhxTreeOtrab = dhxLayoutOtrab.cells("a").attachTree();
	dhxTreeOtrab.setImagePath("dhtmlxSuite/dhtmlxTree/codebase/imgs/"+tree_icons+"/");
	dhxTreeOtrab.setXMLAutoLoading("windows/otrab_vrem/getTree.php");
    dhxTreeOtrab.loadXML("windows/otrab_vrem/getTree.php?id=0");
	var num_load=0;//Переменная счетчика количесва подгрузки дерева
	dhxTreeOtrab.attachEvent("onXLE", function(dhxTreeOtrab,id)
												{
													num_load++;
													dhxTreeOtrab.selectItem(1);//Выбираем по умолчанию верхний уровень
													dhxTreeOtrab.openAllItems(1);
													if(num_load==6 && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")//После окончания загрузки дерева
													{
														dhxTreeOtrab.attachEvent("onSelect", function(id){
															if(WinOtrab_toolbar.getListOptionSelected("set_month")!=null)
															{
																dhxLayoutOtrab_c.cells("a").setText("Использование рабочего времени в течении дня по сотруднику");
																dhxOtrabVrem_c_b.setColumnLabel(0, "Сотрудник", 0);
																WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
																WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
																WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
																WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
																WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
																dhxOtrabVrem_c_a.clearAll();
																dhxOtrabVrem_c_b.clearAll();
																clearInterval(ProgressInterval);
																document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
																document.getElementById("text_info").innerHTML="Обновление таблицы...";
																document.getElementById("procent").innerHTML="";
																document.getElementById("button").innerHTML="";
																dhxWinInfo.setModal(true);
																dhxWinInfo.show();
																statusbarOtrab_b.setText("Обновление таблицы...");
																dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+id+"&listIdChild="+dhxTreeOtrab.getAllSubItems(id));
															}
														 });
														document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
														document.getElementById("text_info").innerHTML="Обновление таблицы...";
														document.getElementById("procent").innerHTML="";
														document.getElementById("button").innerHTML="";
														dhxWinInfo.setModal(true);
														dhxWinInfo.show();
														statusbarOtrab_b.setText("Обновление таблицы...");
														dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));//После 6 загрузки дерева обновляем таблицу
													}
												});
	
		
	WinOtrab_toolbar = dhxLayoutOtrab.cells("b").attachToolbar();	
	WinOtrab_toolbar.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
	WinOtrab_toolbar.setSkin(toolbar_skin);
	
	WinOtrab_toolbar.addText("text_year", 5, "Год: ");
	var printOpts = Array();
	var year_today = new Date().getFullYear();
	var n = 10;//Количество лет для выбора
	for (i=0; i<=n ;i++)
	{
		printOpts[i] = Array(year_today-(n/2)+i, 'obj', year_today-(n/2)+i, '');
	}
	WinOtrab_toolbar.addButtonSelect("set_year", 13, year_today, printOpts, "", "");
	WinOtrab_toolbar.addSeparator("sep1", 20);
	
	
	WinOtrab_toolbar.addText("text_month", 25, "Месяц: ");
	var OptsMonth = Array();
	var month_today = new Date().getMonth()+1;
	for (i=0; i<12 ;i++)
	{
		OptsMonth[i] = Array(i+1, 'obj', monthName[i], '');
	}
	
	WinOtrab_toolbar.addButtonSelect("set_month", 30, "", OptsMonth, "", "");
	WinOtrab_toolbar.setListOptionSelected("set_month", month_today);
	WinOtrab_toolbar.setItemText("set_month", WinOtrab_toolbar.getListOptionText("set_month", month_today));
	WinOtrab_toolbar.addSeparator("sep2", 35);
	
	WinOtrab_toolbar.addButton("update", 40, "Обновить таблицу","reload.gif","reload.gif");
	WinOtrab_toolbar.addSeparator("sep3", 45);
	//WinOtrab_toolbar.disableItem("update");
	
	
	function getDayEnd()
	{
		var loader_day_end = dhtmlxAjax.getSync("windows/otrab_vrem/getInfo.php?action=get_day_end&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"));
		var ArrayDay = new Array();
		ArrayDay = loader_day_end.xmlDoc.responseText.split('|');
		return ArrayDay[1];
	}
	
	
	
	WinOtrab_toolbar.addButton("start_otrab", 47, "Расчитать","ar_right.gif","");
	WinOtrab_toolbar.addText("text_day_beg", 48, "с:");
	WinOtrab_toolbar.addInput("input_day_beg", 50, "1", 18);
	WinOtrab_toolbar.addText("text_day_end", 51, "по:");
	WinOtrab_toolbar.addInput("input_day_end", 52, getDayEnd(), 18);
	WinOtrab_toolbar.addText("text_day", 55, "число");
	
	WinOtrab_toolbar.hideItem("start_otrab");
	WinOtrab_toolbar.hideItem("text_day_beg");
	WinOtrab_toolbar.hideItem("input_day_beg");
	WinOtrab_toolbar.hideItem("text_day_end");
	WinOtrab_toolbar.hideItem("input_day_end");
	WinOtrab_toolbar.hideItem("text_day");
	
	WinOtrab_toolbar.addSeparator("sep4", 56);
	
	WinOtrab_toolbar.addButton("save_svod", 57, "Переформировать свод","ar_right.gif","ar_right_dis.gif");
	WinOtrab_toolbar.addSeparator("sep5", 58);
	
	var OperationList_1 = Array();
	//OperationList_1[0] = Array('start_otrab', 'obj', 'Выполнить расчет', 'star_on.png','star_on_dis.png');
	OperationList_1[0] = Array('clear', 'obj', 'Удалить отработанное время','','');
	OperationList_1[1] = Array('clear_all', 'obj', 'Удалить отработанное время и корректировки','','');

	WinOtrab_toolbar.addButtonSelect('Operations_1', 59, 'Удаление отработанного времени и корректировок', OperationList_1,'delete.gif','delete_dis.gif');
	
	WinOtrab_toolbar.addSeparator("sep6", 60);
	//WinOtrab_toolbar.disableItem("Operations_1");
	WinOtrab_toolbar.hideItem("Operations_1");

	var OperationList_2 = Array();
	OperationList_2[0] = Array('to_excel_otkl', 'obj', 'Ведомости отклонений', 'page_excel.png','page_excel_dis.png');
	OperationList_2[1] = Array('to_excel_su', 'obj', 'Ведомости с/у времени', 'page_excel.png','page_excel_dis.png');

	WinOtrab_toolbar.addButtonSelect('Operations_2', 65, 'Формирование ведомостей', OperationList_2, '', '');
	WinOtrab_toolbar.setListOptionToolTip('Operations_2', 'to_excel_otkl', 'Сформировать ведомости отклонений в Excel');
	WinOtrab_toolbar.setListOptionToolTip('Operations_2', 'to_excel_su', 'Сформировать ведомости с/у времени в Excel');
	WinOtrab_toolbar.addSeparator("sep7", 70);
	//WinOtrab_toolbar.disableItem("Operations_2");
	WinOtrab_toolbar.hideItem("Operations_2");
	
	WinOtrab_toolbar.addSeparator("sep8", 75);
	
	//enableitem
	
	WinOtrab_toolbar.attachEvent("onClick", function(idButOtrab){ 
														
														dhxLayoutOtrab_c.cells("a").setText("Использование рабочего времени в течении дня по сотруднику");
														dhxOtrabVrem_c_b.setColumnLabel(0, "Сотрудник", 0);
														
														if(idButOtrab=="update" && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															dhxOtrabVrem_c_a.clearAll();
															dhxOtrabVrem_c_b.clearAll();
															statusBarOtrabVrem_c_a.setText("");
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
															WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Обновление таблицы...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															statusbarOtrab_b.setText("Обновление таблицы...");
															//WinInfo("Обновление таблицы...");
															dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
															WinOtrab_toolbar.setValue("input_day_end", getDayEnd());
														}
														
														if(idButOtrab>2000 && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															dhxOtrabVrem.setColumnLabel(5, monthName[WinOtrab_toolbar.getListOptionSelected("set_month")-1]+" "+idButOtrab+" г.", 1);
															WinOtrab_toolbar.setItemText("set_year", idButOtrab);
															dhxOtrabVrem_c_a.clearAll();
															dhxOtrabVrem_c_b.clearAll();
															statusBarOtrabVrem_c_a.setText("");
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
															WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Обновление таблицы...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															statusbarOtrab_b.setText("Обновление таблицы...");
															//WinInfo("Обновление таблицы...");
															dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
															WinOtrab_toolbar.setValue("input_day_end", getDayEnd());
														}
														
														if(idButOtrab>0 && idButOtrab<13 && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															WinOtrab_toolbar.setItemText("set_month", WinOtrab_toolbar.getListOptionText("set_month", idButOtrab));
															dhxOtrabVrem.setColumnLabel(5, monthName[idButOtrab-1]+" "+WinOtrab_toolbar.getItemText("set_year")+" г.", 0);
															
															
															
															dhxOtrabVrem_c_a.clearAll();
															dhxOtrabVrem_c_b.clearAll();
															statusBarOtrabVrem_c_a.setText("");
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
															WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Обновление таблицы...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															statusbarOtrab_b.setText("Обновление таблицы...");
															//WinInfo("Обновление таблицы...");
															dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
															WinOtrab_toolbar.setValue("input_day_end", getDayEnd());
														}
														
														
														if(idButOtrab=="save_svod" && WinOtrab_toolbar.getValue("input_day_beg")!="" && WinOtrab_toolbar.getValue("input_day_end")!="" && confirm("Выполнить переформирование сводной таблицы по выбранным записям?\r\r(Если записи не выбраны, то переформирование будет выполнено по всем записям)") && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															var list_id_sel = dhxOtrabVrem.getCheckedRows(0);
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
															WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
															WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
															dhxOtrabVrem.clearAll();
															dhxOtrabVrem_c_a.clearAll();
															dhxOtrabVrem_c_b.clearAll();
															statusBarOtrabVrem_c_a.setText("");
															statusbarOtrab_b.setText("");
															
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Запуск формирования свода...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															menu.setItemDisabled("Сервис"); menu.setItemDisabled("Модули"); menu.setItemDisabled("Справочники");
															//Запускаем процесс обновления процента выполнения запроса
															ProgressInterval = setInterval("Progress('windows/otrab_vrem/get_status.php','1')",1000);
															
															//Отправляем запрос на запуск расчета отработанного времени
															var oXmlHttp_startOtrab = new XMLHttpRequest();
															
															//alert(list_id_sel);
															oXmlHttp_startOtrab.open("get","windows/otrab_vrem/saveSvod.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&list_id_sel="+list_id_sel,true);
															oXmlHttp_startOtrab.onreadystatechange = function()
																								{
																									if(oXmlHttp_startOtrab.readyState==4)
																									{
																										//alert(oXmlHttp_startOtrab.responseText);
																										//Останавливаем процесс обновления процента выполнения запроса
																										clearInterval(ProgressInterval);
																										document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
																										document.getElementById("text_info").innerHTML="Обновление таблицы...";
																										document.getElementById("procent").innerHTML="";
																										document.getElementById("button").innerHTML="";
																										menu.setItemEnabled("Сервис"); menu.setItemEnabled("Модули"); menu.setItemEnabled("Справочники");
																										statusbarOtrab_b.setText("Обновление таблицы...");
																										dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
																												
																									}
																								}
															oXmlHttp_startOtrab.send(null);
															
														}
														
														if(idButOtrab=="start_otrab" && WinOtrab_toolbar.getValue("input_day_beg")!="" && WinOtrab_toolbar.getValue("input_day_end")!="" && confirm("Выполнить расчет за "+WinOtrab_toolbar.getItemText("set_month")+" "+WinOtrab_toolbar.getItemText("set_year")+" года с "+WinOtrab_toolbar.getValue("input_day_beg")+" по "+WinOtrab_toolbar.getValue("input_day_end")+" число?\rДанные выбранного периода будут обновлены!\r"+dhxOtrabVrem.getCheckedRows(0)) && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															//alert("windows/otrab_vrem/startOtrab.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day_beg="+WinOtrab_toolbar.getValue("input_day_beg")+"&day_end="+WinOtrab_toolbar.getValue("input_day_end")+"&list_id_sel="+dhxOtrabVrem.getCheckedRows(0));
															
															var list_id_sel = dhxOtrabVrem.getCheckedRows(0);
															dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=check_tabel_1c&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"),
																function(loader)
																{
																	if(loader.xmlDoc.responseText.substr(3,1)=="0")
																	{
																		alert("Не загружен табель 1С!");
																	}
																	else
																	{
																		WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
																		WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
																		WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
																		WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
																		WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
																		dhxOtrabVrem.clearAll();
																		dhxOtrabVrem_c_a.clearAll();
																		dhxOtrabVrem_c_b.clearAll();
																		statusBarOtrabVrem_c_a.setText("");
																		statusbarOtrab_b.setText("");
																		
																		document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
																		document.getElementById("text_info").innerHTML="Запуск расчета...";
																		document.getElementById("procent").innerHTML="";
																		document.getElementById("button").innerHTML="";
																		dhxWinInfo.setModal(true);
																		dhxWinInfo.show();
																		menu.setItemDisabled("Сервис"); menu.setItemDisabled("Модули"); menu.setItemDisabled("Справочники");
																		//Запускаем процесс обновления процента выполнения запроса
																		ProgressInterval = setInterval("Progress('windows/otrab_vrem/get_status.php','1')",1000);
																		
																		//Отправляем запрос на запуск расчета отработанного времени
																		var oXmlHttp_startOtrab = new XMLHttpRequest();
																		//alert(list_id_sel);
																		oXmlHttp_startOtrab.open("get","windows/otrab_vrem/startOtrab.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day_beg="+WinOtrab_toolbar.getValue("input_day_beg")+"&day_end="+WinOtrab_toolbar.getValue("input_day_end")+"&list_id_sel="+list_id_sel,true);
																		oXmlHttp_startOtrab.onreadystatechange = function()
																											{
																												if(oXmlHttp_startOtrab.readyState==4)
																												{
																													//alert(oXmlHttp_startOtrab.responseText);
																													//Останавливаем процесс обновления процента выполнения запроса
																													clearInterval(ProgressInterval);
																													document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
																													document.getElementById("text_info").innerHTML="Обновление таблицы...";
																													document.getElementById("procent").innerHTML="";
																													document.getElementById("button").innerHTML="";
																													menu.setItemEnabled("Сервис"); menu.setItemEnabled("Модули"); menu.setItemEnabled("Справочники");
																													statusbarOtrab_b.setText("Обновление таблицы...");
																													dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
																															
																												}
																											}
																		oXmlHttp_startOtrab.send(null);
																	}
																});
															
														}
														
														if(idButOtrab=="clear" && confirm("Очистить расчет за "+WinOtrab_toolbar.getItemText("set_month")+" месяц "+WinOtrab_toolbar.getItemText("set_year")+" года?") && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															statusbarOtrab_b.setText("Очистка расчета...");
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Очистка расчета...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															//WinInfo("Очистка расчета...");
															dhtmlxAjax.get("windows/otrab_vrem/startOtrab.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"), function(loader)
																																																				{
																																																					alert(loader.xmlDoc.responseText);
																																																					dhxOtrabVrem_c_a.clearAll();
																																																					dhxOtrabVrem_c_b.clearAll();
																																																					statusBarOtrabVrem_c_a.setText("");
																																																					document.getElementById("img").innerHTML="";
																																																					document.getElementById("text_info").innerHTML="";
																																																					document.getElementById("procent").innerHTML="";
																																																					document.getElementById("button").innerHTML="";
																																																					dhxWinInfo.setModal(false);
																																																					dhxWinInfo.hide();
																																																					//dhxWinInfo.close();
																																																					statusbarOtrab_b.setText("Обновление таблицы...");
																																																					dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
																																																					
																																																				});
														}
														if(idButOtrab=="clear_all" && confirm("Вы действительно хотите выполнить полную очистку расчета за "+WinOtrab_toolbar.getItemText("set_month")+" месяц "+WinOtrab_toolbar.getItemText("set_year")+" года?\rИстория корректировок будет удалена!") && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															statusbarOtrab_b.setText("Полная очистка расчета...");
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Полная очистка расчета...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															//WinInfo("Полная очистка расчета...");
															dhtmlxAjax.get("windows/otrab_vrem/startOtrab.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"), function(loader)
																																																				{
																																																					alert(loader.xmlDoc.responseText);
																																																					dhxOtrabVrem_c_a.clearAll();
																																																					dhxOtrabVrem_c_b.clearAll();
																																																					statusBarOtrabVrem_c_a.setText("");
																																																					document.getElementById("img").innerHTML="";
																																																					document.getElementById("text_info").innerHTML="";
																																																					document.getElementById("procent").innerHTML="";
																																																					document.getElementById("button").innerHTML="";
																																																					dhxWinInfo.setModal(false);
																																																					dhxWinInfo.hide();
																																																					//dhxWinInfo.close();
																																																					statusbarOtrab_b.setText("Обновление таблицы...");
																																																					dhxOtrabVrem.clearAndLoad("windows/otrab_vrem/getGrid2.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()));
																																																					
																																																				});
														}
														
														if(idButOtrab=="to_excel_otkl" && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Формирование ведомостей в Excel...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															//WinInfo("Формирование ведомостей в Excel...");
															dhtmlxAjax.get("windows/otrab_vrem/toExcel.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"), 
																function(loader)
																{
																	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=check_file&file_name=otkl_"+WinOtrab_toolbar.getItemText("set_month")+"_"+WinOtrab_toolbar.getItemText("set_year")+".zip", 
																		function(loader)
																		{
																			document.getElementById("img").innerHTML="";
																			document.getElementById("text_info").innerHTML="";
																			document.getElementById("procent").innerHTML="";
																			document.getElementById("button").innerHTML="";
																			dhxWinInfo.setModal(false);
																			dhxWinInfo.hide();
																			//dhxWinInfo.close();
																			if(loader.xmlDoc.responseText==1) open("http://webk05/windows/otrab_vrem/reports/otkl_"+WinOtrab_toolbar.getItemText("set_month")+"_"+WinOtrab_toolbar.getItemText("set_year")+".zip"); 
																			else alert("Ошибка выгрузки!");
																		});
																});
														}
														if(idButOtrab=="to_excel_su" && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
															document.getElementById("text_info").innerHTML="Формирование ведомостей в Excel...";
															document.getElementById("procent").innerHTML="";
															document.getElementById("button").innerHTML="";
															dhxWinInfo.setModal(true);
															dhxWinInfo.show();
															//WinInfo("Формирование ведомостей в Excel...");
															dhtmlxAjax.get("windows/otrab_vrem/toExcel.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"), 
																function(loader)
																{
																	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=check_file&file_name=su_"+WinOtrab_toolbar.getItemText("set_month")+"_"+WinOtrab_toolbar.getItemText("set_year")+".zip", 
																		function(loader)
																		{
																			document.getElementById("img").innerHTML="";
																			document.getElementById("text_info").innerHTML="";
																			document.getElementById("procent").innerHTML="";
																			document.getElementById("button").innerHTML="";
																			dhxWinInfo.setModal(false);
																			dhxWinInfo.hide();
																			//dhxWinInfo.close();
																			if(loader.xmlDoc.responseText==1) open("http://webk05/windows/otrab_vrem/reports/su_"+WinOtrab_toolbar.getItemText("set_month")+"_"+WinOtrab_toolbar.getItemText("set_year")+".zip"); 
																			else alert("Ошибка выгрузки!");
																		});
																});
														}
													});
	

	dhxOtrabVrem = dhxLayoutOtrab.cells("b").attachGrid(); 
	dhxOtrabVrem.setHeader(",Таб.,{#collapse}3:ФИО,Подр.,Итого (час.),Месяц,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");//Наименования заголовков
	dhxOtrabVrem.attachHeader("#rspan,#select_filter_strict,#select_filter_strict,#select_filter_strict,#rspan,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31");
	dhxOtrabVrem.setInitWidths("24,40,100,50,45,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*,*");//Ширина столбцов
	dhxOtrabVrem.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
	dhxOtrabVrem.setColAlign("center,center,left,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
	dhxOtrabVrem.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
	dhxOtrabVrem.setSkin(grid_skin);//Оформление
	dhxOtrabVrem.setColSorting("na,int,str,str,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
	dhxOtrabVrem.enableMultiline(true);
	//dhxOtrabVrem.enableSmartRendering(true,50);
	dhxOtrabVrem.init();
	dhxOtrabVrem.collapseColumns(2);
	dhxOtrabVrem.setColumnLabel(5, WinOtrab_toolbar.getItemText("set_year")+" г.", 0);
	
	var last_id_sel; //Переменная последней отмеченной строки
	dhxOtrabVrem.attachEvent("onCheck", function(rId,cInd,state)
										{
											//alert(state);
											if(state)//Если ставим галочку
											{
												if(window.event.shiftKey)//Если клавиша shift нажата
												{
													if(dhxOtrabVrem.cellById(last_id_sel,0).getValue()==1)
													{
														//alert(dhxOtrabVrem.getRowIndex(rId)+"_"+dhxOtrabVrem.getRowIndex(last_id_sel));
														if(dhxOtrabVrem.getRowIndex(rId)<dhxOtrabVrem.getRowIndex(last_id_sel))
														{
															var indRow1 = dhxOtrabVrem.getRowIndex(rId);
															var indRow2 = dhxOtrabVrem.getRowIndex(last_id_sel);
														}
														else
														{
															var indRow1 = dhxOtrabVrem.getRowIndex(last_id_sel);
															var indRow2 = dhxOtrabVrem.getRowIndex(rId);
														}
														
														for(i=indRow1+1; i<indRow2 ; i++)
														{
															dhxOtrabVrem.cellByIndex(i,0).setValue(1);
														}
													}
												}
												last_id_sel = rId;
											}
											return true;
										});
	
	dhxOtrabVrem.attachEvent("onRowSelect", function(rId,cInd)
											{
												if(CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
												{
													//Отображаем ФИО сотрудника
													dhxLayoutOtrab_c.cells("a").setText("("+dhxOtrabVrem.cellById(rId,1).getValue()+")  "+dhxOtrabVrem.cellById(rId,2).getValue());
													dhxOtrabVrem_c_b.setColumnLabel(0, dhxOtrabVrem.cellById(rId,2).getValue(), 0);
													
													if(cInd>4)
													{
														//Проверяем статус дня и наличие записей
														dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=check_records_for_enable_button&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(cInd-4)+"&tabn="+dhxOtrabVrem.cellById(rId,1).getValue(), 
																function(loader)
																	{
																		//alert(loader.xmlDoc.responseText);
																		if(loader.xmlDoc.responseText==1)
																		{
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
																			WinOtrab_toolbar_c_a.enableListOption('add_del_records', 'add_vid_isp_vrem_2');
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
																			WinOtrab_toolbar_c_b.enableItem("add_in_out");
																			WinOtrab_toolbar_c_b.disableItem("del_in_out");
																		}
																		else if(loader.xmlDoc.responseText==2)
																		{
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
																			WinOtrab_toolbar_c_a.enableListOption('add_del_records', 'add_vid_isp_vrem_3');
																			WinOtrab_toolbar_c_b.enableItem("add_in_out");
																			WinOtrab_toolbar_c_b.disableItem("del_in_out");
																		}
																		else if(loader.xmlDoc.responseText==3)
																		{
																			WinOtrab_toolbar_c_a.enableListOption('add_del_records', 'add_vid_isp_vrem_1');
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
																			WinOtrab_toolbar_c_b.enableItem("add_in_out");
																			WinOtrab_toolbar_c_b.disableItem("del_in_out");
																		}
																		else if(loader.xmlDoc.responseText==0)
																		{
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
																			WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
																			WinOtrab_toolbar_c_b.disableItem("add_in_out");
																			WinOtrab_toolbar_c_b.disableItem("del_in_out");
																		}
																	});
														
														
														WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
														WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
														
														WinOtrab_toolbar.enableListOption("update_otkl", "otkl_update_rows");
														if(dhxOtrabVrem.cellById(rId,cInd).getValue()!="")WinOtrab_toolbar.enableListOption("update_otkl", "otkl_update_cell"); else WinOtrab_toolbar.disableListOption("update_otkl", "otkl_update_cell");

														var day = cInd-4;
														dhxOtrabVrem_c_b.clearAndLoad("windows/otrab_vrem/getGrid_c_b.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+day+"&tabn="+dhxOtrabVrem.cellById(rId,1).getValue());																														
														dhxOtrabVrem_c_a.clearAndLoad("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+day+"&tabn="+dhxOtrabVrem.cellById(rId,1).getValue());																														
														
														dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=get_DOVS_in_1C&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(cInd-4)+"&tabn="+dhxOtrabVrem.cellById(rId,1).getValue(), 
																function(loader)
																	{
																		var ArrayRes =loader.xmlDoc.responseText.split('|');
																		if((ArrayRes[1]-ArrayRes[2])!=0)statusBarOtrabVrem_c_a.setText("<font color='red'>ВНИМАНИЕ! Продолжительность отпуска без содержания в табеле 1С:</font> <font color='blue'>"+ArrayRes[1]+"</font><font color='red'> час., в учете: </font><font color='blue'>"+ArrayRes[2]+"</font><font color='red'> час.!</font>");
																		else statusBarOtrabVrem_c_a.setText("");
																	});
														
													}
													else
													{
														dhxOtrabVrem_c_a.clearAll();
														dhxOtrabVrem_c_b.clearAll();
														WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
														WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
														WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
														WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
														WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
														WinOtrab_toolbar_c_b.disableItem("add_in_out");
														WinOtrab_toolbar_c_b.disableItem("del_in_out");
													}
													
													statusbarOtrab_b.setText("");
												}
												
											});
											
	dhxOtrabVrem.attachEvent("onXLE", function(grid_obj,count)
											{
												WinOtrab_toolbar.enableItem("update");
												WinOtrab_toolbar.enableItem("Operations_1");
												WinOtrab_toolbar.enableItem("Operations_2");
												dhxOtrabVrem.setColumnLabel(0,"#master_checkbox",1);
												dhxOtrabVrem.sortRows(2,"str","asc");
												statusbarOtrab_b.setText("");
												document.getElementById("img").innerHTML="";
												document.getElementById("text_info").innerHTML="";
												document.getElementById("procent").innerHTML="";
												document.getElementById("button").innerHTML="";
												dhxWinInfo.setModal(false);
												dhxWinInfo.hide();
												//dhxWinInfo.close();
											});	
	dhxOtrabVrem.attachEvent("onKeyPress", function(code,cFlag,sFlag)
											{
												if(char_code[code])
												{
													var new_str = statusbarOtrab_b.getText()+char_code[code];
													statusbarOtrab_b.setText(new_str);
													
													dhtmlxAjax.get("windows/otrab_vrem/search.php?str_fam="+statusbarOtrab_b.getText()+"&idSel="+dhxTreeOtrab.getSelectedItemId()+"&listIdChild="+dhxTreeOtrab.getAllSubItems(dhxTreeOtrab.getSelectedItemId()),
															function(loader)
																{
																	var ArrayRes = new Array();
																	ArrayRes = loader.xmlDoc.responseText.split('|');
																	dhxOtrabVrem.selectCell(dhxOtrabVrem.getRowIndex(ArrayRes[1]),2);
																});
													
													
												}
												else
												{
													return true;
												}
												//if(code==39 && dhxOtrabVrem.getSelectedCellIndex()<dhxOtrabVrem.getColumnsNum()-1) dhxOtrabVrem.selectCell(dhxOtrabVrem.getRowIndex(dhxOtrabVrem.getSelectedRowId()),dhxOtrabVrem.getSelectedCellIndex()+1);
												//if(code==37)dhxOtrabVrem.selectCell(dhxOtrabVrem.getRowIndex(dhxOtrabVrem.getSelectedRowId()),dhxOtrabVrem.getSelectedCellIndex()-1);
											});

	
	dhxLayoutOtrab_c.cells("a").setText("Использование рабочего времени в течении дня по сотруднику");
	dhxLayoutOtrab_c.cells("b").setText("Посещения в течении дня по сотруднику");
	dhxLayoutOtrab_c.cells("b").setWidth(450);
	
	WinOtrab_toolbar_c_a = dhxLayoutOtrab_c.cells("a").attachToolbar();	
	WinOtrab_toolbar_c_a.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
	WinOtrab_toolbar_c_a.setSkin(toolbar_skin);
	var ActionList = Array();
	
	ActionList[0] = Array('add_vid_isp_vrem_1', 'obj', 'Добавить период работы в сверхурочное время без фиксации в СКУД', '');
	ActionList[1] = Array('add_vid_isp_vrem_2', 'obj', 'Добавить период работы в выходной день без фиксации в СКУД', '');
	ActionList[2] = Array('add_vid_isp_vrem_3', 'obj', 'Добавить период работы на гибком режиме без фиксации в СКУД', '');
	ActionList[3] = Array('del_vid_isp_vrem', 'obj', 'Удалить период работы без фиксации в СКУД', '');

	WinOtrab_toolbar_c_a.addButtonSelect('add_del_records', 5, 'Добавление и удаление периодов работы без фиксации в СКУД', ActionList, '', '');
	WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
	WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
	WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
	WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
	WinOtrab_toolbar_c_a.hideItem('add_del_records');

	WinOtrab_toolbar_c_a.addSeparator("sep1", 10);
	WinOtrab_toolbar_c_a.addButton("del_edit_vid_isp_vrem", 100, "Удалить корректировку","package-purge.png","package-purge_dis.png");
	WinOtrab_toolbar_c_a.setItemToolTip("del_edit_vid_isp_vrem", "Удалить корректировку использования рабочего времени");
	WinOtrab_toolbar_c_a.addSeparator("sep5", 110);
	WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
	WinOtrab_toolbar_c_a.hideItem("del_edit_vid_isp_vrem");
	
	WinOtrab_toolbar_c_a.attachEvent("onClick", function(idButOtrab)
													{
														if(idButOtrab=="add_vid_isp_vrem_1" && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															dhtmlxAjax.get("windows/otrab_vrem/edit_vid_isp_vrem.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(), 
															function(loader)
																{
																	dhxOtrabVrem_c_a.updateFromXML("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
																	statusBarOtrabVrem_c_a.setText("");
																});
														}
														
														if(idButOtrab=="add_vid_isp_vrem_2" && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															dhtmlxAjax.get("windows/otrab_vrem/edit_vid_isp_vrem.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(), 
															function(loader)
																{
																	dhxOtrabVrem_c_a.updateFromXML("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
																	statusBarOtrabVrem_c_a.setText("");
																	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=update_cell&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),
																			function(loader)
																			{
																				var ArrayCell =loader.xmlDoc.responseText.split('|');
																				dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex()).setValue(ArrayCell[0]);
																				dhxOtrabVrem.setCellTextStyle(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex(),ArrayCell[1]);
																				dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),4).setValue(ArrayCell[2]);
																				WinOtrab_toolbar_c_a.disableItem("add_vid_isp_vrem_2");
																			});
																});
														}
														
														if(idButOtrab=="add_vid_isp_vrem_3" && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															dhtmlxAjax.get("windows/otrab_vrem/edit_vid_isp_vrem.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(), 
															function(loader)
																{
																	dhxOtrabVrem_c_a.updateFromXML("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
																	statusBarOtrabVrem_c_a.setText("");
																});
														}
														
														if(idButOtrab=="del_vid_isp_vrem" && dhxOtrabVrem_c_a.getSelectedRowId()!=null && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															if(confirm("Удалить выбранный период использования рабочего времени?"))
															{
															dhtmlxAjax.get("windows/otrab_vrem/edit_vid_isp_vrem.php?action="+idButOtrab+"&id="+dhxOtrabVrem_c_a.getSelectedRowId(), 
															function(loader)
																{
																	dhxOtrabVrem_c_a.updateFromXML("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
																	statusBarOtrabVrem_c_a.setText("");
																	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=update_cell&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),
																			function(loader)
																			{
																				WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_1');
																				WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_2');
																				WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'add_vid_isp_vrem_3');
																				WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
																				
																				var ArrayCell =loader.xmlDoc.responseText.split('|');
																				dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex()).setValue(ArrayCell[0]);
																				dhxOtrabVrem.setCellTextStyle(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex(),ArrayCell[1]);
																				dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),4).setValue(ArrayCell[2]);
																				
																				//Обновление использования рабочего времени
																				document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
																				document.getElementById("text_info").innerHTML="Обновление использования рабочего времени...";
																				document.getElementById("procent").innerHTML="";
																				document.getElementById("button").innerHTML="";
																				dhxWinInfo.setModal(true);
																				dhxWinInfo.show();
																				//alert("windows/ispolz_vrem/save.php?action=start_isp_vrem&list_tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue()+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"));
																				dhtmlxAjax.get("windows/ispolz_vrem/save.php?action=start_isp_vrem&list_tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue()+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"),
																				function(loader)
																				{
																					document.getElementById("img").innerHTML="";
																					document.getElementById("text_info").innerHTML="";
																					document.getElementById("procent").innerHTML="";
																					document.getElementById("button").innerHTML="";
																					dhxWinInfo.setModal(false);
																					dhxWinInfo.hide();
																				});
																			});
																});
															}
														}
														
														if(idButOtrab=="del_edit_vid_isp_vrem" && dhxOtrabVrem_c_a.getSelectedRowId()!=null && CheckStartGroupProcess("otrab_vrem","dhxWinOtrab")=="off")
														{
															if(confirm("Удалить корректировку вида использования рабочего времени?"))
															{
															dhtmlxAjax.get("windows/otrab_vrem/edit_vid_isp_vrem.php?action="+idButOtrab+"&id="+dhxOtrabVrem_c_a.getSelectedRowId(), 
															function(loader)
																{
																	WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
																	dhxOtrabVrem_c_a.updateFromXML("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
																	
																	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=update_cell&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),
																			function(loader)
																			{
																				var ArrayCell =loader.xmlDoc.responseText.split('|');
																				dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex()).setValue(ArrayCell[0]);
																				dhxOtrabVrem.setCellTextStyle(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex(),ArrayCell[1]);
																				dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),4).setValue(ArrayCell[2]);
																				
																				//Обновление использования рабочего времени
																				document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
																				document.getElementById("text_info").innerHTML="Обновление использования рабочего времени...";
																				document.getElementById("procent").innerHTML="";
																				document.getElementById("button").innerHTML="";
																				dhxWinInfo.setModal(true);
																				dhxWinInfo.show();
																				
																				dhtmlxAjax.get("windows/ispolz_vrem/save.php?action=start_isp_vrem&list_tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue()+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"),
																				function(loader)
																				{
																					document.getElementById("img").innerHTML="";
																					document.getElementById("text_info").innerHTML="";
																					document.getElementById("procent").innerHTML="";
																					document.getElementById("button").innerHTML="";
																					dhxWinInfo.setModal(false);
																					dhxWinInfo.hide();
																				});
																			
																			});
																			
																	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=get_DOVS_in_1C&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(), 
																	function(loader)
																		{
																			var ArrayRes =loader.xmlDoc.responseText.split('|');
																			if((ArrayRes[1]-ArrayRes[2])!=0)statusBarOtrabVrem_c_a.setText("<font color='red'>ВНИМАНИЕ! Продолжительность отпуска без содержания в табеле 1С:</font> <font color='blue'>"+ArrayRes[1]+"</font><font color='red'> час., в учете: </font><font color='blue'>"+ArrayRes[2]+"</font><font color='red'> час.!</font>");
																			else statusBarOtrabVrem_c_a.setText("");
																		});
																		
																});
															}
														}
														
														
														
														
													});
	
	
	WinOtrab_toolbar_c_b = dhxLayoutOtrab_c.cells("b").attachToolbar();	
	WinOtrab_toolbar_c_b.setIconsPath("dhtmlxSuite/dhtmlxToolbar/samples/common/imgs/");
	WinOtrab_toolbar_c_b.setSkin(toolbar_skin);
	WinOtrab_toolbar_c_b.addButton("add_in_out", 10, "Добавить период","new.gif","new_dis.gif");
	WinOtrab_toolbar_c_b.disableItem("add_in_out");
	WinOtrab_toolbar_c_b.hideItem("add_in_out");
	WinOtrab_toolbar_c_b.addSeparator("sep1", 20);
	WinOtrab_toolbar_c_b.addButton("del_in_out", 30, "Удалить период","delete.gif","delete_dis.gif");
	WinOtrab_toolbar_c_b.disableItem("del_in_out");
	WinOtrab_toolbar_c_b.hideItem("del_in_out");
	WinOtrab_toolbar_c_b.addSeparator("sep2", 40);
	
	WinOtrab_toolbar_c_b.attachEvent("onClick", function(idButOtrab)
													{
														if(idButOtrab=="add_in_out" && confirm("Добавить период присутствия?"))
														{
															//alert("windows/otrab_vrem/functions.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue());
															dhxLayoutOtrab_c.cells("b").progressOn();
															dhtmlxAjax.get("windows/otrab_vrem/functions.php?action="+idButOtrab+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(), 
															function(loader)
																{
																	dhxOtrabVrem_c_b.clearAndLoad("windows/otrab_vrem/getGrid_c_b.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue());
																	dhxLayoutOtrab_c.cells("b").progressOff();
																});
														}
														if(idButOtrab=="del_in_out" && confirm("Удалить выбранный период присутствия?"))
														{
															dhxLayoutOtrab_c.cells("b").progressOn();
															dhtmlxAjax.get("windows/otrab_vrem/functions.php?action="+idButOtrab+"&id="+dhxOtrabVrem_c_b.getSelectedRowId(), 
															function(loader)
																{
																	dhxOtrabVrem_c_b.clearAndLoad("windows/otrab_vrem/getGrid_c_b.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue());
																	dhxLayoutOtrab_c.cells("b").progressOff();
																});
														}
														
														
														
														
													});
	
	
	dhxOtrabVrem_c_a = dhxLayoutOtrab_c.cells("a").attachGrid();
	dhxOtrabVrem_c_a.setHeader("<img src='dhtmlxSuite/dhtmlxGrid/codebase/imgs/iconWrite1.gif'>,Начало,#cspan,Окончание,#cspan,Продолжит.,#cspan,Период дня,Вид использования рабочего времени");//Наименования заголовков
	dhxOtrabVrem_c_a.attachHeader("#rspan,Дата,Время,Дата,Время,чч:мм:сс,час.,#rspan,#rspan");
	dhxOtrabVrem_c_a.setInitWidths("25,65,55,65,55,60,35,150,*");//Ширина столбцов
	dhxOtrabVrem_c_a.enableTooltips("false,false,false,false,false,false,false,false,true");
	dhxOtrabVrem_c_a.setColAlign("center,center,center,center,center,center,center,left,left");
	dhxOtrabVrem_c_a.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
	dhxOtrabVrem_c_a.setSkin(grid_skin);//Оформление
	dhxOtrabVrem_c_a.init();

	
	dhxOtrabVrem_c_b = dhxLayoutOtrab_c.cells("b").attachGrid();
	dhxOtrabVrem_c_b.setHeader("Сотрудник,#cspan,#cspan,#cspan,#cspan,#cspan");//Наименования заголовков
	dhxOtrabVrem_c_b.attachHeader("Вход,#cspan,#cspan,Выход,#cspan,#cspan");
	dhxOtrabVrem_c_b.attachHeader("Дата,Время,№ прох.,Дата,Время,№ прох.");
	dhxOtrabVrem_c_b.setInitWidths("65,55,70,65,55,70");//Ширина столбцов
	dhxOtrabVrem_c_b.enableTooltips("false,false,false,false,false,false");
	dhxOtrabVrem_c_b.setColAlign("center,center,center,center,center,center");
	dhxOtrabVrem_c_b.setImagePath("dhtmlxSuite/dhtmlxGrid/codebase/imgs/");
	dhxOtrabVrem_c_b.setSkin(grid_skin);//Оформление
	dhxOtrabVrem_c_b.init();
	

	dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=get_id_profile",
			function(loader)
				{
					var ArrayRes = new Array();
					ArrayRes = loader.xmlDoc.responseText.split('|');
					if(ArrayRes[1]=="4" || ArrayRes[1]=="5")
					{
						WinOtrab_toolbar.showItem("start_otrab");
						WinOtrab_toolbar.showItem("text_day_beg");
						WinOtrab_toolbar.showItem("input_day_beg");
						WinOtrab_toolbar.showItem("text_day_end");
						WinOtrab_toolbar.showItem("input_day_end");
						WinOtrab_toolbar.showItem("text_day");
						
						WinOtrab_toolbar.showItem("Operations_1");
						//WinOtrab_toolbar.showItem("Operations_2");
						WinOtrab_toolbar_c_a.showItem("add_del_records");
						WinOtrab_toolbar_c_a.showItem("del_edit_vid_isp_vrem");
						WinOtrab_toolbar_c_b.showItem("add_in_out");
						WinOtrab_toolbar_c_b.showItem("del_in_out");
						
						dhxOtrabVrem_c_a.attachEvent("onRowSelect", function(id,ind)
								{
									dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=vid_isp_vrem&id="+id,
																function(loader)
																{
																	if(loader.xmlDoc.responseText.substr(3,1)=="7" || loader.xmlDoc.responseText.substr(3,2)=="24" || loader.xmlDoc.responseText.substr(3,2)=="32")WinOtrab_toolbar_c_a.enableListOption('add_del_records', 'del_vid_isp_vrem');
																	else WinOtrab_toolbar_c_a.disableListOption('add_del_records', 'del_vid_isp_vrem');
																});
									
									//Определяем наличие истории корректировок по выбранному периоду
									dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=get_isp_vrem_edit&id="+id,
																function(loader)
																{
																	if(loader.xmlDoc.responseText.substr(3,1)!="")WinOtrab_toolbar_c_a.enableItem("del_edit_vid_isp_vrem");
																	else WinOtrab_toolbar_c_a.disableItem("del_edit_vid_isp_vrem");
																});
								});
							
							dhxOtrabVrem_c_a.attachEvent("onRowDblClicked", function(rId,cInd)
								{
									
									if(cInd==8) 
									{
										//Определяем вид использования рабочего времени
										dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=check_edit_isp_vrem&id="+rId,
																function(loader)
																{
																//Открываем окно справочника видов использования рабочего времени
																if(loader.xmlDoc.responseText.substr(3,1)=="1") {WinSpr_vid_isp_vrem("spr_vid_isp_vrem",1,rId);}
																else {alert("Данный вид использования рабочего времени не редактируется!");}
																
																
																});
										
										
									}
									if(cInd==5) 
									{
										dhxOtrabVrem_c_a.editCell();
									}
								});

							dhxOtrabVrem_c_a.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue)
								{
									if(stage==2 && nValue!=oValue && nValue.length==8 && nValue.substr(2,1)==":" && nValue.substr(5,1)==":")
									{
										//Сохранение изменений
										document.getElementById("img").innerHTML="<img src='http://webk05/dhtmlxSuite/dhtmlxWindows/codebase/imgs/dhxacc_cell_progress.gif'>";
										document.getElementById("text_info").innerHTML="Сохранение...";
										document.getElementById("procent").innerHTML="";
										document.getElementById("button").innerHTML="";
										dhxWinInfo.setModal(true);
										dhxWinInfo.show();
										//alert(rId);
										dhtmlxAjax.get("windows/otrab_vrem/startOtrab.php?action=edit_period_time&id="+rId+"&period_time="+nValue, 
												function(loader)
													{
														document.getElementById("text_info").innerHTML="Обновление таблицы...";
														dhxOtrabVrem_c_a.updateFromXML("windows/otrab_vrem/getGrid_c_a.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
														statusBarOtrabVrem_c_a.setText("");
														dhtmlxAjax.get("windows/otrab_vrem/getInfo.php?action=update_cell&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),
																function(loader)
																{
																	var ArrayCell =loader.xmlDoc.responseText.split('|');
																	dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex()).setValue(ArrayCell[0]);
																	dhxOtrabVrem.setCellTextStyle(dhxOtrabVrem.getSelectedRowId(),dhxOtrabVrem.getSelectedCellIndex(),ArrayCell[1]);
																	dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),4).setValue(ArrayCell[2]);
																	
																	//Обновление использования рабочего времени
																	document.getElementById("text_info").innerHTML="Обновление использования рабочего времени...";
																	
																	dhtmlxAjax.get("windows/ispolz_vrem/save.php?action=start&list_tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue()+"&year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month"),
																	function(loader)
																	{
																		document.getElementById("img").innerHTML="";
																		document.getElementById("text_info").innerHTML="";
																		document.getElementById("procent").innerHTML="";
																		document.getElementById("button").innerHTML="";
																		dhxWinInfo.setModal(false);
																		dhxWinInfo.hide();
																	});
																	
																});
													});
										return true;
									}
								});
								
							dhxOtrabVrem_c_b.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue)
								{
									if(stage==2 && nValue!=oValue && (((cInd==1 || cInd==4) && nValue.length==8 && nValue.substr(2,1)==":" && nValue.substr(5,1)==":") || ((cInd==0 || cInd==3) && nValue.length==10 && nValue.substr(2,1)=="." && nValue.substr(5,1)==".")))
									{
										if(cInd==0) var type_in_out="in_date";
										if(cInd==1) var type_in_out="in_time";
										if(cInd==3) var type_in_out="out_date";
										if(cInd==4) var type_in_out="out_time";
										
										dhxLayoutOtrab_c.cells("b").progressOn();
										dhtmlxAjax.get("windows/otrab_vrem/functions.php?action=edit_in_out&type="+type_in_out+"&id="+rId+"&value="+nValue,
										function(loader)
											{
												dhxOtrabVrem_c_b.updateFromXML("windows/otrab_vrem/getGrid_c_b.php?year="+WinOtrab_toolbar.getItemText("set_year")+"&month="+WinOtrab_toolbar.getListOptionSelected("set_month")+"&day="+(dhxOtrabVrem.getSelectedCellIndex()-4)+"&tabn="+dhxOtrabVrem.cellById(dhxOtrabVrem.getSelectedRowId(),1).getValue(),true,true);
												dhxLayoutOtrab_c.cells("b").progressOff();
											});
										
										return true;
									}
									
								});
								
							dhxOtrabVrem_c_b.attachEvent("onRowSelect", function(id,ind)
								{
									WinOtrab_toolbar_c_b.enableItem("del_in_out");
								});
					}
					
				});
	
	
	
	
	CheckStartGroupProcess("otrab_vrem","dhxWinOtrab");
	
}