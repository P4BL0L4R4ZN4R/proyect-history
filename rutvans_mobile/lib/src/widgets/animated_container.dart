import 'package:flutter/material.dart';

class AnimatedFadeSlideContainer extends StatelessWidget {
  final Widget child;
  final Duration duration;
  final Offset initialOffset;
  
  const AnimatedFadeSlideContainer({
    super.key,
    required this.child,
    this.duration = const Duration(milliseconds: 500),
    this.initialOffset = const Offset(0, 0.2),
  });

  @override
  Widget build(BuildContext context) {
    return TweenAnimationBuilder(
      tween: Tween(begin: 0.0, end: 1.0),
      duration: duration,
      builder: (_, double value, __) {
        return Opacity(
          opacity: value,
          child: Transform.translate(
            offset: initialOffset * (1 - value),
            child: child,
          ),
        );
      },
    );
  }
}