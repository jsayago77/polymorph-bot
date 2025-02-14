import React, { useState, useRef, useEffect } from 'react';
import { motion, AnimatePresence } from "motion/react";
import ChatbotLite from './ChatbotLite';

const AnimatedIcon = () => {
    const [position, setPosition] = useState({ x: 0, y: 0 });
    const [isDragging, setIsDragging] = useState(false);
    const gifRef = useRef(null);
    const intervalRef = useRef(null);
    const dragStartPos = useRef({ x: 0, y: 0 });
    const [showChat, setShowChat] = useState(false);
    const [avatar, setAvatar] = useState(polybotObj.avatar.default);

    // Movimiento aleatorio
    const moveRandomly = () => {
        if (!gifRef.current) return;
        if (showChat) return;

        const maxX = window.innerWidth - gifRef.current.offsetWidth;
        const maxY = window.innerHeight - gifRef.current.offsetHeight;
        const maxStep = 50;

        const newX = Math.max(0, Math.min(
            position.x + (Math.random() * maxStep * 2 - maxStep),
            maxX
        ));

        const newY = Math.max(0, Math.min(
            position.y + (Math.random() * maxStep * 2 - maxStep),
            maxY
        ));

        setPosition({
            x: newX,
            y: newY
        });
    };

    // Iniciar movimiento automático
    useEffect(() => {
        if (!isDragging) {
            intervalRef.current = setInterval(moveRandomly, 3000);
            return () => clearInterval(intervalRef.current);
        }
    }, [isDragging, position]);

    // Eventos de arrastre
    const handleMouseDown = (e) => {
        setIsDragging(true);
        clearInterval(intervalRef.current);
        dragStartPos.current = {
            x: e.clientX - position.x,
            y: e.clientY - position.y
        };
    };

    const handleMouseMove = (e) => {
        if (isDragging && gifRef.current) {
            const newX = e.clientX - dragStartPos.current.x;
            const newY = e.clientY - dragStartPos.current.y;

            // Mantener dentro de los límites
            const maxX = window.innerWidth - gifRef.current.offsetWidth;
            const maxY = window.innerHeight - gifRef.current.offsetHeight;

            setPosition({
                x: Math.min(Math.max(newX, 0), maxX),
                y: Math.min(Math.max(newY, 0), maxY)
            });
        }
    };

    const handleMouseUp = () => {
        setIsDragging(false);
    };

    const openChat = () => {
        setShowChat(true);
    }

    const closeChat = () => {
        setShowChat(false);
    }

    return (
        <div
            ref={gifRef}
            style={{
                position: 'fixed',
                left: position.x,
                top: position.y,
                zIndex: 9999,
                transition: isDragging ? 'none' : 'all 2s ease-in-out',
                display: 'flex',
                width: '25%'
            }}
        >
            <div 
            style={{ minWidth: '27%', width: '27%', cursor: isDragging ? 'grabbing' : 'grab', }}
            onMouseDown={handleMouseDown}
            onMouseMove={handleMouseMove}
            onMouseUp={handleMouseUp}
            onMouseLeave={handleMouseUp}
            onClick={() => openChat()}
            >
                <img
                    src={avatar}
                    alt="Draggable GIF"
                    style={{ pointerEvents: 'none', marginLeft: '0'}}
                    draggable="false"
                />
            </div>
            {showChat &&
                <AnimatePresence>
                    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }} exit={{ opacity: 0 }}>
                        <ChatbotLite handleClose={closeChat} handleAvatar={setAvatar} />
                    </motion.div>
                </AnimatePresence>
            }
        </div>
    );
};

export default AnimatedIcon;