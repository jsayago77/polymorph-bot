import React, { useState, useCallback } from 'react';
import Chatbot from './chatbot';
import { motion, AnimatePresence } from "motion/react";

const BubbleIcon = () => {
  const [showIcon, setShowIcon] = useState(true);
  const [top, setTop] = useState('75%');
  const [right, setRight] = useState('-5%');

  const openChat = () => {
    setShowIcon(false);
    setTop('15%');
    setRight('1%');
  }

  const closeChat = () => {
    setShowIcon(true);
    setTop('75%');
    setRight('-5%')
  }

  return (
    <>
      <div style={{ position: 'fixed', top: top, right: right, zIndex: 1000 }}>
        {showIcon ?
          <motion.div
            whileHover={{ scale: 1.3 }}
          >
            <img src={polybotObj.avatar.default} style={{ width: '40%' }} onClick={() => openChat()} />
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
