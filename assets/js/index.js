import domReady from '@wordpress/dom-ready';
import BubbleIcon from './components/BubbleIcon';
import VerticalIcon from './components/VerticalIcon';
import { createRoot } from '@wordpress/element';
import '../css/index.css';

domReady( () => {
    const shortcode_root = createRoot(
        document.getElementById( 'polybot_chatbot' )
    );

    shortcode_root.render( <BubbleIcon /> );
} );