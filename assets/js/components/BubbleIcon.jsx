import React, { useState, useCallback } from 'react';
import Chatbot from './chatbot';
import { motion, AnimatePresence } from "motion/react";

const BubbleIcon = () => {
  const [showIcon, setShowIcon] = useState(true);
  const [top, setTop] = useState('75%');
  const [right, setRight] = useState('-370px');

  const openChat = () => {
    setShowIcon(false);
    if(isMobile()){
      setTop('0%');
      setRight('0%');
    } else {
      setTop('15%');
      setRight('1.5%');
    }
  }

  const closeChat = () => {
    setShowIcon(true);
    setTop('75%');
    setRight('-370px')
  }

  const isMobile = () => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  };

  return (
    <>
      <div style={{ position: 'fixed', top: top, right: right, zIndex: 'calc(infinity)' }}>
        {showIcon ?
          <motion.div
            whileHover={{ scale: 1.3 }}
            style={{
              width: '20%'
            }}
          >
            <img src={polybotObj.avatar.default} style={{ marginLeft: '0' }} onClick={() => openChat()} />
          </motion.div>
          :
          <AnimatePresence>
            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }} exit={{ opacity: 0 }}>
              <Chatbot handleClose={closeChat} />
            </motion.div>
          </AnimatePresence>
        }
      </div>
    </>
  );
};

export default BubbleIcon;
