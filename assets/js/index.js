import domReady from '@wordpress/dom-ready';
import BotIcon from './components/botIcon';
import { createRoot } from '@wordpress/element';
import '../css/index.css';

domReady( () => {
    const shortcode_root = createRoot(
        document.getElementById( 'polybot_chatbot' )
    );

    shortcode_root.render( <BotIcon /> );
} );