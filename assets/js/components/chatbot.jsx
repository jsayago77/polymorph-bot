import React, { useState, useEffect, useRef } from 'react';
import { Comment } from 'react-loader-spinner';
import MarkdownRenderer from './mkdRenderer';

import CloseIcon from '../../img/xmark-solid.svg';

const hiBot = polybotObj.avatar.default;
const shokedBot = polybotObj.avatar.userSend;
const errorBot = polybotObj.avatar.error;
const writingBot = polybotObj.avatar.writing;
const thinkBot = polybotObj.avatar.userWriting;

const Chatbot = ({ handleClose }) => {
    const messagesEndRef = useRef(null);
    const [messages, setMessages] = useState([]);
    const [thread, setThread] = useState('');
    const [currentMessage, setCurrentMessage] = useState('');
    const [loading, setLoading] = useState(false);
    const [limit, setLimit] = useState(0);
    const [frequent, setFrequent] = useState([
        '¿Qué es Example?',
        '¿Qué cursos ofrece Example?',
        'Ayúdame con el material de un curso...'
    ]);
    const [sendQ, setSendQ] = useState(false);

    const [botState, setBotState] = useState(hiBot);

    useEffect(() => {
        if (currentMessage !== '') sendMessage();
    }, []);

    const makeQuestion = (message) => {
        setCurrentMessage(message);
        setSendQ(!sendQ);

    }

    const handleCleanMessages = () => {
        setThread('');
        setMessages([]);
    }

    useEffect(() => {
        if (currentMessage !== '') sendMessage();
    }, [sendQ]);

    const handleChange = (event) => {
        setCurrentMessage(event.target.value);
    };

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollTo({
            top: messagesEndRef.current.scrollHeight,
            behavior: 'smooth'
        });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages, loading]);

    const sendMessage = async () => {

        var send_message = currentMessage;
        setCurrentMessage('');

        const newMessage = {
            id: messages.length + 1,
            text: send_message,
            sender: 'user'
        };
        setMessages([...messages, newMessage]);
        if (send_message.trim() !== '') {
            setLoading(true);
            setBotState(writingBot);
            try {
                const response = await fetch(ubiChatObj.chatApiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: send_message, thread_id: thread })
                });

                if (response.ok) {
                    const resp = await response.json();

                    const chatbotMessage = {
                        id: messages.length + 1,
                        text: resp.msg,
                        sender: 'assistant'
                    };

                    setThread(resp.thread);
                    setBotState(hiBot);
                    setLoading(false);
                    setMessages([...messages, newMessage, chatbotMessage]);
                } else {
                    setBotState(errorBot);
                    setLoading(false);
                    setMessages([...messages, newMessage, { id: messages.length + 1, text: '¡Ha ocurrido un error! Inténtalo de nuevo.', sender: 'assistant' }]);
                    throw new Error('Error al enviar el mensaje');
                }
            } catch (error) {
                console.error(error);
            }
        }
    }

    const handleSubmit = (event) => {
        event.preventDefault();
        sendMessage();
        setLimit(limit + 1);
    };

    const renderMessages = () => {

        const userMessages = messages.filter(message => message.sender === 'user');

        return messages.map((message) => (
            <div key={message.id} className={`message ${message.sender}`}>
                <MarkdownRenderer markdown={message.text} />
            </div>
        ));
    };

    return (
        <>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <div className="flex flex-col bg-white rounded-lg shadow-lg w-96 h-[500px]">
                <div className="bg-blue-500 text-white p-4 rounded-t-lg flex">
                    <div className='w-17 h-17 bg-white m-4 rounded-full'>
                        <img src={botState}/>
                    </div>
                    <div className='text-lg font-semibold'>{polybotObj.name}</div>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'end', padding: '.5em' }}>
                        <img src={CloseIcon} style={{ width: '7%' }} onClick={() => handleClose()} />
                    </div>
                </div>
                <div className="flex-1 p-4 overflow-y-auto space-y-2" ref={messagesEndRef}>
                    {!messages.length && /*limit != 5 &&*/ (
                        <div className='assistant-init'>
                            <p className='assistant-name'>¡Hola! ¿En qué puedo ayudarte hoy?</p>
                            <div className='frequent-questions'>
                                {frequent.map((message) => (
                                    <div onClick={() => makeQuestion(message)}><span>{message}</span></div>
                                ))}
                            </div>
                            {/* <div className='limit-message'>
                                    Debes saber que tienes 5 preguntas disponibles diariamente.
                                </div> */}
                        </div>
                    )}
                    <div className="chatbot-chat">
                        {renderMessages()}
                        {loading && (
                            <>
                                <Comment
                                    key={-1}
                                    visible={loading}
                                    height="40"
                                    width="50"
                                    ariaLabel="comment-loading"
                                    wrapperStyle={{ margin: "0", padding: "0" }}
                                    wrapperClass="message assistant"
                                    color="#ffffff"
                                    backgroundColor="#76bd1d"
                                />
                            </>
                        )}
                    </div>
                    {/* { limit == 5 && (
                            <div className='limit-message'>Haz alcanzado el límite diario de mensajes.</div>
                        )} */}
                </div>
                <form onSubmit={handleSubmit}>
                    <div className='p-4 border-t border-gray-200'>
                        <div className='flex'>
                            <button
                                color="primary"
                                type="button"
                                onClick={() => handleCleanMessages()}
                                className='bg-blue-500 text-white p-2 rounded-r-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500'
                            >
                                <i className="fa-solid fa-broom"></i>
                            </button>
                            <input
                                value={currentMessage}
                                onChange={handleChange}
                                onFocus={() => setBotState(thinkBot)}
                                onBlur={() => setBotState(hiBot)}
                                placeholder="Escribe tu mensaje..."
                                type="text"
                                disabled={false /*limit == 5 ? true : false*/}
                            />
                            <button
                                color="primary"
                                type="submit"
                                disabled={false /*limit == 5 ? true : false*/}
                                onMouseEnter={() => setBotState(shokedBot)}
                                onMouseLeave={() => setBotState(hiBot)}
                                className='bg-blue-500 text-white p-2 rounded-r-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500'
                            >
                                <i className="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
};

export default Chatbot;