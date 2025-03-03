import React, { useState, useCallback } from 'react';
import Chatbot from './chatbot';
import { motion, AnimatePresence } from "motion/react";

const VerticalIcon = () => {
  const [showIcon, setShowIcon] = useState(true);
  const [top, setTop] = useState('44%');
  const [right, setRight] = useState('-5%');

  const openChat = () => {
    setShowIcon(false);
    setTop('15%');
    setRight('1%');
  }

  const closeChat = () => {
    setShowIcon(true);
    setTop('44%');
    setRight('-5%')
  }

  return (
    <>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
      <div style={{ position: 'fixed', top: top, right: '0%', zIndex: 1000 }}>
        {showIcon ?
          <motion.div
            whileHover={{ scale: 1.3 }}
          >
            <div style={{ border: 'solid .2em' + polybotObj.color, padding: '1.5em .2em', borderRadius: '.3em 0 0 .3em', backgroundColor: polybotObj.color }} onClick={() => openChat()}>
                <i class="fa-solid fa-caret-left fa-lg" style={{ color: '#ffffff'}}></i>
            </div>
          </motion.div>
          :
          <AnimatePresence>
            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }} exit={{ opacity: 0 }}>
              <Chatbot handleClose={closeChat} style={{}} />
            </motion.div>
          </AnimatePresence>
        }
      </div>
    </>
  );
};

export default VerticalIcon;
