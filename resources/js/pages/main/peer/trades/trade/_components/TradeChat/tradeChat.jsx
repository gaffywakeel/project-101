import React, {useCallback, useContext, useEffect, useRef} from "react";
import PeerTradeContext from "@/contexts/PeerTradeContext";
import {Card, Divider, Stack} from "@mui/material";
import {styled} from "@mui/material/styles";
import ChatHeader from "./ChatHeader";
import {useChatParticipants} from "./hooks";
import {ChatProvider} from "@/contexts/ChatContext";
import ChatMessages from "./ChatMessages";
import SendMessage from "./SendMessage";
import {usePresenceBroadcast, usePrivateBroadcast} from "@/services/Broadcast";
import audioFile from "@/static/audio/message.mp3";
import TradeAction from "./TradeAction";
import {errorHandler, route, useRequest} from "@/services/Http";
import TradeRating from "./TradeRating";
import useMountHandler from "@/hooks/useMountHandler";

const TradeChat = () => {
    const messagesRef = useRef();
    const handler = useMountHandler();
    const participants = useChatParticipants();
    const {trade, fetchTrade} = useContext(PeerTradeContext);
    const [request] = useRequest();

    const chatBroadcast = usePresenceBroadcast(trade.conversationChannel());
    const broadcast = usePrivateBroadcast(trade.channel());

    const reloadMessages = useCallback(() => {
        messagesRef.current?.resetPage();
    }, []);

    const markAsRead = useCallback(() => {
        request
            .post(route("peer-trade.mark-read", {trade: trade.id}))
            .catch(errorHandler());
    }, [request, trade]);

    useEffect(() => {
        const {fetchParticipants} = participants;

        chatBroadcast
            .joining(() => {
                handler.execute(fetchParticipants);
            })
            .leaving(() => {
                handler.execute(fetchParticipants);
            });
    }, [handler, chatBroadcast, participants]);

    useEffect(() => {
        const channel = "ChatMessageSent";
        const handler = (e) => {
            reloadMessages();
            markAsRead();
            alertAudio.play();
        };

        chatBroadcast.listen(channel, handler);

        return () => {
            chatBroadcast.stopListening(channel, handler);
        };
    }, [chatBroadcast, reloadMessages, markAsRead]);

    useEffect(() => {
        const channel = ".PeerTradeUpdated";
        const handler = () => fetchTrade();

        broadcast.listen(channel, handler);

        return () => {
            broadcast.stopListening(channel, handler);
        };
    }, [broadcast, fetchTrade]);

    useEffect(() => {
        markAsRead();
    }, [markAsRead]);

    return (
        <ChatProvider
            {...participants}
            reloadMessages={reloadMessages}
            messagesRef={messagesRef}>
            <StyledCard>
                <ChatHeader />
                <Divider />

                <Stack direction="row" sx={{overflow: "hidden"}} flexGrow={1}>
                    <ChatContent />
                    <TradeAction />
                </Stack>
            </StyledCard>
        </ChatProvider>
    );
};

const ChatContent = () => {
    const {trade} = useContext(PeerTradeContext);

    return trade.ratable() ? (
        <TradeRating />
    ) : (
        <Stack flexGrow={1}>
            <ChatMessages />
            <Divider />
            <SendMessage />
        </Stack>
    );
};

const alertAudio = new Audio(audioFile);

const StyledCard = styled(Card)(({theme}) => ({
    display: "flex",
    height: "calc(100vh - 176px)",
    flexDirection: "column",
    flexGrow: 1,
    [theme.breakpoints.up("lg")]: {
        height: "calc(100vh - 232px)"
    }
}));

export default TradeChat;
