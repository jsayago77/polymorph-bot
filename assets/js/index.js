import domReady from '@wordpress/dom-ready';
import BubbleIcon from './components/BubbleIcon';
import VerticalIcon from './components/VerticalIcon';
import AnimatedIcon from './components/AnimatedIcon';
import { createRoot } from '@wordpress/element';
import '../css/index.css';

domReady( () => {
    const shortcode_root = createRoot(
        document.getElementById( 'polybot_chatbot' )
    );
    switch (polybotObj.ui) {
        case 'animation':
            shortcode_root.render( <AnimatedIcon /> );
            break;
        case 'bubble':
            shortcode_root.render( <BubbleIcon /> );
            break;
    }
        
    
} );