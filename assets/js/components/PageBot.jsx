import React, { useState, useEffect, useRef } from 'react';
import { Comment } from 'react-loader-spinner';
import MarkdownRenderer from './mkdRenderer';

const hiBot = polybotObj.avatar.default;
const shokedBot = polybotObj.avatar.userSend;
const errorBot = polybotObj.avatar.error;
const writingBot = polybotObj.avatar.writing;
const thinkBot = polybotObj.avatar.userWriting;

const PageBot = ({ handleClose }) => {
    const messagesEndRef = useRef(null);
    const [messages, setMessages] = useState([]);
    const [thread, setThread] = useState('');
    const [currentMessage, setCurrentMessage] = useState('');
    const [loading, setLoading] = useState(false);
    const [limit, setLimit] = useState(0);
    const [frequent, setFrequent] = useState([
        '¿Qué es este sitio?',
        '¿Qué servicios ofrecen?',
        'Ayúdame a resolver una duda'
    ]);
    const [sendQ, setSendQ] = useState(false);

    const [botState, setBotState] = useState(hiBot);

    useEffect(() => {
        const chatbotMessage = {
            id: 0,
            text: '¡Hola! ¿En qué puedo ayudarte hoy?',
            sender: 'assistant'
        };

        setMessages([...messages, chatbotMessage]);
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
                const response = await fetch(polybotObj.messageUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ bot_id: polybotObj.botID, message: send_message, thread_id: thread })
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
            <div 
                key={message.id} 
                className={`my-2 !p-2 rounded-md text-white font-semibold max-w-180 mb-0 ${message.sender === 'user' ? 'bg-blue-400 justify-self-end' : 'justify-self-start'}`}
                style={message.sender !== 'user' ? { backgroundColor: polybotObj.color } : {}}>
                <MarkdownRenderer markdown={message.text} />
            </div>
        ));
    };

    const isMobile = () => {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    };

    return (
        <div className='w-full flex justify-center'>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <div className="polybot-chatbot flex flex-col bg-white rounded-lg shadow-lg w-400 h-[500px] max-[400px]:w-screen max-[400px]:h-screen">
                <div className="text-white rounded-t-lg flex justify-between" style={{ backgroundColor: polybotObj.color}}>
                    <div className='w-25 bg-white !m-4 rounded-full'>
                        <img style={{ marginLeft: '0' }} src={botState} />
                    </div>
                    <div className='flex-1 items-center content-center font-semibold w-62'>{polybotObj.name}</div>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'end', padding: '1em' }}>
                        <button
                            type="button"
                            onClick={() => handleCleanMessages()}
                            className="option-button"
                        >
                            <i className="fa-solid fa-broom"></i>
                        </button>
                        <button type="button" onClick={() => {window.location.href = polybotObj.generalBotPage;} } className="option-button">
                            <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                        </button>
                    </div>
                </div>
                <div className="flex-1 !p-4 overflow-y-auto space-y-2 message-container" ref={messagesEndRef}>
                    {!messages.length && /*limit != 5 &&*/ (
                        <div className='assistant-init'>
                            {/* <p className='assistant-name'>¡Hola! ¿En qué puedo ayudarte hoy?</p>
                            <div className='frequent-questions'>
                                {frequent.map((message) => (
                                    <div onClick={() => makeQuestion(message)}><span>{message}</span></div>
                                ))}
                            </div> */}
                            {/* <div className='limit-message'>
                                    Debes saber que tienes 5 preguntas disponibles diariamente.
                                </div> */}
                        </div>
                    )}
                    <div className="flex-1 !p-4 overflow-y-auto space-y-2">
                        {renderMessages()}
                        {loading && (
                            <div>
                                <Comment
                                    key={-1}
                                    visible={loading}
                                    height="40"
                                    width="50"
                                    ariaLabel="comment-loading"
                                    wrapperStyle={{ margin: "0", padding: "0", backgroundColor: polybotObj.color }}
                                    wrapperClass="!m-2 rounded-md text-white font-semibold w-fit"
                                    color="#ffffff"
                                    backgroundColor={polybotObj.color}
                                />
                            </div>
                        )}
                    </div>
                    {/* { limit == 5 && (
                            <div className='limit-message'>Haz alcanzado el límite diario de mensajes.</div>
                        )} */}
                </div>
                <form onSubmit={handleSubmit}>
                    <div className='border-t border-gray-200'>
                        <div className='flex'>
                            <input
                                value={currentMessage}
                                onChange={handleChange}
                                onFocus={() => setBotState(thinkBot)}
                                onBlur={() => setBotState(hiBot)}
                                placeholder="Escribe tu mensaje..."
                                type="text"
                                disabled={false /*limit == 5 ? true : false*/}
                                style={{ border: '0'}}
                                className='poly-input'
                            />
                            <button
                                type="submit"
                                disabled={false /*limit == 5 ? true : false*/}
                                onMouseEnter={() => setBotState(shokedBot)}
                                onMouseLeave={() => setBotState(hiBot)}
                                className='send-button'
                            >
                                <i className="fa-regular fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default PageBot;