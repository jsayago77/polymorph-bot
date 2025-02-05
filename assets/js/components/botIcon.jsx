import React, { useState, useCallback } from 'react';
import Chatbot from './chatbot';
//import * as motion from "motion/react-client";

const BotIcon = () => {
  const [showIcon, setShowIcon] = useState(true);
  const [top, setTop] = useState('80%');

  const openChat = () => {
    setShowIcon(false);
    setTop('15%');
  }

  const closeChat = () => {
    setShowIcon(true);
    setTop('80%');    
  }

  return (
    <>
    <div style={{ position: 'fixed', top: top, right: '1%', zIndex: 1000 }}>
      { showIcon ?
        <div><img src={polybotObj.avatar.default} style={{ width: '70%' }} onClick={() => openChat() }/></div>
      :
        // <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }} exit={{ opacity: 0 }}>
          <Chatbot handleClose={ closeChat } />
        // </motion.div>
      }      
    </div>
    </>
  );
};

export default BotIcon;
