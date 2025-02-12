import domReady from '@wordpress/dom-ready';
import BubbleIcon from './components/BubbleIcon';
import VerticalIcon from './components/VerticalIcon';
import AnimatedIcon from './components/AnimatedIcon';
import { createRoot } from '@wordpress/element';
import '../css/index.css';

const isMobile = () => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
};

domReady( () => {
    const shortcode_root = createRoot(
        document.getElementById( 'polybot_chatbot' )
    );
    switch (polybotObj.ui) {
        case 'animation':
            if(isMobile())
                shortcode_root.render( <BubbleIcon /> );
            else
                shortcode_root.render( <AnimatedIcon /> );
            break;
        case 'bubble':
            shortcode_root.render( <BubbleIcon /> );
            break;
    }
        
    
} );