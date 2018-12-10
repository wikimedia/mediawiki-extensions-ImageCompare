<?php

class ImageCompare {
	public static function onExtensionLoad() {}
	
	public static function onParserInit(Parser $parser) {
		$parser->setHook('imgcomp', 'ImageCompare::parseTag');
	}
	
	public static function parseTag($input, array $args, Parser $parser, PPFrame $frame) {
		try {
			$parser->getOutput()->addModules( 'ext.imageCompare' );
			$parser->getOutput()->addModuleStyles( 'ext.imageCompare.styles' );
			if (!isset($args['img1'], $args['img2'])) {
				throw new ImageCompareException("error-noimg");
			}
			$divheight = 0;
			$width = null;
			if (isset($args['width']))
				if (is_numeric($args['width']))
					$width = round($args['width']);
				else throw new ImageCompareException("error-numberinvalid");
			$return = ImageCompare::makeImage($parser, Title::newFromDBKey(str_replace(' ', '_', 'File:'.$args['img2'].'')), 'img-comp-img', $divheight, $width)
			.ImageCompare::makeImage($parser, Title::newFromDBKey(str_replace(' ', '_', 'File:'.$args['img1'].'')), 'img-comp-img img-comp-overlay', $divheight, $width)
			.'</div>';
			return "<div class='img-comp-container' style='height: {$divheight}px;'>".$return;
		} catch (ImageCompareException $e) {
			return wfMessage('ImageCompare-'.$e->getMessage())->parse();
		}
	}

	# Modified code from Parser.
	public static function makeImage($parser, $title, $classes, &$divheight, $width) {
		# Fetch and register the file (file title may be different via hooks)
		list( $file, $title ) = $parser->fetchFileAndTitle( $title, "" );

		$imgheight = 0;
		
		# Linker does the rest
		$time = isset( $options['time'] ) ? $options['time'] : false;
		$ret = ImageCompare::makeImageLink($parser, $title, $file, $classes, $imgheight, $width);
		
		if ($imgheight > $divheight) $divheight = $imgheight;

		return $ret;
	}
	
	# Modified code from Linker.
	public static function makeImageLink(Parser $parser, Title $title, $file, $classes, &$imgheight, $width) {
		$res = null;
		$dummy = new DummyLinker;
		if ( !Hooks::run( 'ImageBeforeProduceHTML', [ &$dummy, &$title,
			&$file, &$frameParams, &$handlerParams, &$time, &$res ] ) ) {
			return $res;
		}

		if (!$file) {
			throw new ImageCompareException("error-imginvalid");
		}
		
		$prefix = $postfix = '';

		$handlerparams['width'] = $file->getWidth( isset( $handlerParams['page'] ) ? $handlerParams['page'] : false );
		if (isset($width) && $width < $handlerparams['width']) $handlerparams['width'] = $width;
		$thumb = $file->transform( $handlerparams );
		if ( !$thumb ) {
			$s = Linker::makeBrokenImageLinkObj( $title, $frameParams['title'], '', '', '', $time == true );
		} else {
			$imgheight = $thumb->getHeight( isset( $handlerParams['page'] ) ? $handlerParams['page'] : false );
			Linker::processResponsiveImages( $file, $thumb, $handlerParams );
			$s = $thumb->toHtml( [] );
		}
		$s = "<div class=\"float {$classes}\">{$s}</div>";
		return str_replace( "\n", ' ', $prefix . $s . $postfix );
	}
}