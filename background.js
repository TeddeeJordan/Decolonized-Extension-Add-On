var clickit = 0;

chrome.browserAction.onClicked.addListener(function(tab) {
	if (clickit == 0){
		chrome.tabs.executeScript(tab.id, {file:"content.js"}); //execute for this tab
		clickit++;
		//alert("Clickit was 0 and now is " + clickit);
	}else if (clickit == 1){
		chrome.tabs.executeScript(tab.id, {file:"colonized.js"}); // revert for this tab
		clickit++;
		//alert("Clickit was 1 and now is " + clickit);
	}else{
		chrome.tabs.executeScript(tab.id, {file:"content.js"}); //execute for this tab
		clickit = 1;
		//alert("Clickit was neither and now is " + clickit);
	}
	
});