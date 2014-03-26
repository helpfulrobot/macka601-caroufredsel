<?php

class CarouFredSelPage extends Page {
	private static $db = array(
		'GenAutoStart' => 'Boolean',
		'GenItemNumberToStart' => 'Int',
	    'GenDurationBeforeStart' => 'Varchar',
	    'GenAlignment' => 'Varchar',
	    'GenItemsMin' => 'Int',
	    'GenItemsMax' => 'Int',
	    'GenItemWidth' => 'Int',
	    'GenItemHeight' => 'Int',
		'GenCarouselCircular' => 'Boolean',
		'GenCarouselInfinite' => 'Boolean',
	    'ScrollDirection' => 'Varchar',
	    'ScrollNumItems' => 'Int',
	    'ScrollEasing' => 'Varchar',
	    'ScrollDuration' => 'Int',
	    'ScrollPauseOnHover' => 'Boolean',
	    'ScrollFx' => 'Varchar',
	    'SwipeOnMouse' => 'Boolean',
	    'SwipeOnTouch' => 'Boolean',
	);

	public static $many_many = array(
	    'Images' => 'Image'	
	);
    
	public function getCMSFields() {
  
	    $ScrollDirectionOptions = array("up", "down", "left", "right"); 
	    
	    $ScrollEasingOptions = array("Elastic", "Linear");
	    
	    $ScrollFx = array("none", "scroll", "directscroll", "fade", "crossfade",
			       "cover", "cover-fade", "uncover", "uncover-fade");
	    
	    $GenAlignment = array ("Left", "Center", "Right");
	    
	    $fields = parent::getCMSFields();

            $fields->addFieldToTab(
		'Root.Photos',  
		$uploadField = new UploadField(
		$name = 'Images',
		$title = 'Upload one or more images (max 10 at a time)')
	    );

	    $fields->addFieldToTab('Root.Settings.General', new CheckBoxField('GenAutoStart','Auto Start Carousel?'));
		$fields->addFieldToTab('Root.Settings.General', new CheckBoxField('GenCarouselCircular','Make Carousel Circular?'));
		$fields->addFieldToTab('Root.Settings.General', new CheckBoxField('GenCarouselInfinite','Make Carousel Infinite?'));
	    $fields->addFieldToTab('Root.Settings.General', new NumericField('GenItemNumberToStart',
	      'Which item should start carousel (0 for random)'));

	    $fields->addFieldToTab('Root.Settings.General', new NumericField('GenDurationBeforeStart',
	      'Delay before carousel scrolls for first time (millisec)'));
	    $fields->addFieldToTab('Root.Settings.General', new NumericField('GenItemsMin','Items Viewable Minimum'));
	    $fields->addFieldToTab('Root.Settings.General', new NumericField('GenItemsMax','Items Viewable Maximum'));
	    $fields->addFieldToTab('Root.Settings.General', new NumericField('GenItemWidth',
	      'Set image width (use 0 for no scaling)'));
	    $fields->addFieldToTab('Root.Settings.General', new NumericField('GenItemHeight',
	      'Set image Height (use 0 for no scaling)'));
	    $fields->addFieldToTab('Root.Settings.General',
		new DropDownField ("GenAlignment", "Slide Alignment?",
		$GenAlignment));

	    $fields->addFieldToTab('Root.Settings.Scroll',
		new DropDownField ("ScrollDirection", "What direction to move the carousel?",
		$ScrollDirectionOptions	));
		
	    $fields->addFieldToTab('Root.Settings.Scroll',
		new DropDownField ("ScrollFx", "Slide Transition type?",
		$ScrollFx));
		
	    $fields->addFieldToTab('Root.Settings.Scroll',
		new DropDownField ("ScrollEasing", "Slide Easing Option",
		$ScrollEasingOptions));
			 
	    $fields->addFieldToTab('Root.Settings.Scroll', new NumericField('ScrollNumItems', 
		'Scroll number of items (0 will scroll none)'));
	    $fields->addFieldToTab('Root.Settings.Scroll', new NumericField('ScrollDuration', 'Scroll Duration Time (millisec)'));
	    $fields->addFieldToTab('Root.Settings.Scroll', new CheckBoxField('ScrollPauseOnHover', 'Pause the scrolling when mouse is hovering?'));
	    
	    $fields->addFieldToTab('Root.Settings.Swipe', new CheckBoxField('SwipeOnMouse',
	      'scroll via dragging (on non-touch-devices only)'));
	    $fields->addFieldToTab('Root.Settings.Swipe', new CheckBoxField('SwipeOnTouch',
	      'scroll via swiping gestures (on touch-devices only)'));
	    	    	    
	    $uploadField->setFolderName('slides'); 
		    
	    $uploadField->setAllowedMaxFileNumber(10);

	    return $fields;   

	    }
}

class CarouFredSelPage_Controller extends Page_Controller {

      Public function init() {
	    
	    parent::init();
   
	    Requirements::customScript(
		  $this->getGlobalSettingsData()
		. $this->getGenSettingsData()
		. $this->getScrollSettingsData()
		. $this->getSwipeSettingsData()
		. "});});");

		Requirements::css("CarouFredSel/css/carouFredSel.css");
	    Requirements::javascript("caroufredsel/javascript/jquery-1.8.2.min.js");
	    Requirements::javascript("caroufredsel/javascript/jquery.carouFredSel-6.2.1-packed.js");
	    Requirements::javascript("caroufredsel/javascript/helper-plugins/jquery.mousewheel.min.js");
	    Requirements::javascript("caroufredsel/javascript/helper-plugins/jquery.touchSwipe.min.js");
	    Requirements::javascript("caroufredsel/javascript/helper-plugins/jquery.touchSwipe.min.js");
	    Requirements::javascript("caroufredsel/javascript/helper-plugins/jquery.ba-throttle-debounce.min.js");

	}

	function getGlobalSettingsData() {
	    $javascript = "$(window).load(function()
	      { 				
		  // Using custom configuration
		  $(\"#carousel\").carouFredSel(
		  {";
	    
	    $ScrollDirectionOptions = array("up", "down", "left", "right"); 
	    
	    $javascript .= "direction: \"".$ScrollDirectionOptions[$this->ScrollDirection]."\",";
	    $javascript .= "prev : \"#prev_img\", next : \"#next_img\", pagination : \"#pager_icon\",";
	    $javascript .= "mousewheel	: true,";
		

	    if($this->GenAutoStart == 1)
	    {
	      $javascript .= "auto : true,";
	    }
	    else
	    {
	      $javascript .= "auto : false,";
	    }
	    
		if($this->GenCarouselInfinite == 1)
		{
			$javascript .= "infinite : true,";
		}
		
		if($this->GenCarouselCircular == 1)
		{
			$javascript .= "circular : true,";
		}
		
	    $GenAlignment = array ("Left", "Center", "Right");
	    
	    $javascript .= "align : \"".$GenAlignment[$this->GenAlignment]."\",";	    
		
		if($this->GenAutoStart == 1 && $this->GenDurationBeforeStart > 0)
		{
			$javascript .= "delay : \"".$this->GenDurationBeforeStart."\",";	    
		}
		
		return $javascript;
	}
	
	function getSwipeSettingsData() {
	      $codeToReturn = "	swipe: {";
	      
	      if($this->SwipeOnMouse == 1)
	      {
		  $codeToReturn .= "onMouse: true,";
	      }
	      
	      if($this->SwipeOnTouch == 1)
	      {
		  $codeToReturn .= "onTouch: true,";
	      }
	      
	      $codeToReturn .= "}";
	      
	      return $codeToReturn;
	}
	
	function getScrollSettingsData() {
		$codeToReturn = "	scroll : {";
	      
		$codeToReturn .= "items : ".$this->ScrollNumItems.",";
	      	    
		$ScrollEasingOptions = array("elastic", "linear");
	      
		$codeToReturn .= "easing : \"".$ScrollEasingOptions[$this->ScrollEasing]."\",";
	    
		if($this->ScrollDuration > 0)
		{
			$codeToReturn .= "duration : ".$this->ScrollDuration.",";
		}
	      
		if($this->ScrollPauseOnHover == 1)
		{
			$codeToReturn .= "pauseOnHover : 'true',";
		}

		$ScrollFx = array("none", "scroll", "directscroll", "fade", "crossfade",
			"cover", "cover-fade", "uncover", "uncover-fade");

		$codeToReturn .= "fx : \"".$ScrollFx[$this->ScrollFx]."\",";

		$codeToReturn .= "},";

		return $codeToReturn;
	}
	
	function getGenSettingsData() {

	      $codeToReturn = " items :{";
	  
	      if($this->GenItemNumberToStart == 0)
	      {
		    $codeToReturn .= "start : \"random\",";
	      }
	      else
	      {
		    $codeToReturn .= "start : ".$this->GenItemNumberToStart.",";
	      }
	      
	      // if one or the other are not empty
	      if($this->GenItemsMin != 0 || $this->GenItemsMax !=0)
	      {
		    $codeToReturn .= "visible : { ";

		    if($this->GenItemsMin > 0)
		    {
				$codeToReturn .= "min : ".$this->GenItemsMin.",";
		    }
		  
		    if($this->GenItemsMax > 0)
		    {
				$codeToReturn .= "max : ".$this->GenItemsMax.",";
		    }
		  
		    $codeToReturn .= "},"; 
	      }
	      
	      $codeToReturn .= "},";
	      
	      return $codeToReturn;
	}
}